import { NextResponse } from "next/server";
import { Prisma } from "@prisma/client";

import { auth } from "@/lib/auth";
import { starterBlocks } from "@/lib/blocks";
import { apiError, unknownApiError } from "@/lib/http";
import { prisma } from "@/lib/prisma";
import { slugify } from "@/lib/utils";
import { getValidationMessage, portfolioCreateSchema } from "@/lib/validation";

export const runtime = "nodejs";

export async function GET() {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);

    const portfolios = await prisma.portfolio.findMany({
      where: { userId: session.user.id },
      orderBy: { updatedAt: "desc" },
      select: { id: true, title: true, slug: true, description: true, publishedAt: true, updatedAt: true }
    });
    return NextResponse.json({ portfolios });
  } catch (error) {
    return unknownApiError(error);
  }
}

export async function POST(request: Request) {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);

    const payload: unknown = await request.json().catch(() => ({}));
    const result = portfolioCreateSchema.safeParse(payload);
    if (!result.success) return apiError(getValidationMessage(result.error), 400);

    const suffix = crypto.randomUUID().slice(0, 6);
    const baseSlug = slugify(result.data.title) || "portfolio";
    const portfolio = await prisma.portfolio.create({
      data: {
        userId: session.user.id,
        title: result.data.title,
        slug: `${baseSlug}-${suffix}`,
        blocks: starterBlocks() as unknown as Prisma.InputJsonValue
      },
      select: { id: true, slug: true }
    });

    return NextResponse.json({ portfolio }, { status: 201 });
  } catch (error) {
    return unknownApiError(error);
  }
}
