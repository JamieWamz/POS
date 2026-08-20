import { NextResponse } from "next/server";
import { z } from "zod";

import { auth } from "@/lib/auth";
import { apiError, unknownApiError } from "@/lib/http";
import { prisma } from "@/lib/prisma";

const publishSchema = z.object({ published: z.boolean() });

export async function POST(request: Request, { params }: { params: { id: string } }) {
  try {
    const session = await auth();
    if (!session?.user.id) return apiError("Authentication required.", 401);
    const payload: unknown = await request.json().catch(() => null);
    const result = publishSchema.safeParse(payload);
    if (!result.success) return apiError("A publication state is required.", 400);

    const owned = await prisma.portfolio.findFirst({ where: { id: params.id, userId: session.user.id }, select: { id: true } });
    if (!owned) return apiError("Portfolio not found.", 404);
    const portfolio = await prisma.portfolio.update({
      where: { id: owned.id },
      data: { publishedAt: result.data.published ? new Date() : null },
      select: { publishedAt: true, slug: true }
    });
    return NextResponse.json({ portfolio });
  } catch (error) {
    return unknownApiError(error);
  }
}
