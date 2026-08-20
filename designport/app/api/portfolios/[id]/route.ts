import { Prisma } from "@prisma/client";
import { NextResponse } from "next/server";

import { auth } from "@/lib/auth";
import { apiError, unknownApiError } from "@/lib/http";
import { prisma } from "@/lib/prisma";
import { getValidationMessage, portfolioUpdateSchema } from "@/lib/validation";

export const runtime = "nodejs";

type Context = { params: { id: string } };

export async function GET(_: Request, { params }: Context) {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);
    const portfolio = await prisma.portfolio.findFirst({ where: { id: params.id, userId: session.user.id } });
    if (!portfolio) return apiError("Portfolio not found.", 404);
    return NextResponse.json({ portfolio });
  } catch (error) {
    return unknownApiError(error);
  }
}

export async function PATCH(request: Request, { params }: Context) {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);

    const payload: unknown = await request.json().catch(() => null);
    const result = portfolioUpdateSchema.safeParse(payload);
    if (!result.success) return apiError(getValidationMessage(result.error), 400);

    const owned = await prisma.portfolio.findFirst({
      where: { id: params.id, userId: session.user.id },
      select: { id: true }
    });
    if (!owned) return apiError("Portfolio not found.", 404);

    const portfolio = await prisma.portfolio.update({
      where: { id: owned.id },
      data: result.data as Prisma.PortfolioUpdateInput,
      select: { id: true, title: true, slug: true, description: true, blocks: true, theme: true, publishedAt: true, updatedAt: true }
    });
    return NextResponse.json({ portfolio });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return apiError("That portfolio URL is already in use.", 409);
    }
    return unknownApiError(error);
  }
}

export async function DELETE(_: Request, { params }: Context) {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);
    const result = await prisma.portfolio.deleteMany({ where: { id: params.id, userId: session.user.id } });
    if (result.count === 0) return apiError("Portfolio not found.", 404);
    return new NextResponse(null, { status: 204 });
  } catch (error) {
    return unknownApiError(error);
  }
}
