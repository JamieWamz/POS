import type { Metadata } from "next";
import { notFound, redirect } from "next/navigation";

import { Editor } from "@/components/builder/editor";
import { auth } from "@/lib/auth";
import { prisma } from "@/lib/prisma";
import { portfolioBlockSchema } from "@/lib/validation";
import type { PortfolioBlock, PortfolioDocument } from "@/types/portfolio";

export const metadata: Metadata = { title: "Portfolio builder" };

export default async function BuilderPage({ params }: { params: { id: string } }) {
  const session = await auth();
  if (!session?.user.id) redirect("/auth/signin");
  const record = await prisma.portfolio.findFirst({ where: { id: params.id, userId: session.user.id } });
  if (!record) notFound();

  const parsedBlocks = Array.isArray(record.blocks)
    ? record.blocks.map((block) => portfolioBlockSchema.safeParse(block)).filter((result) => result.success).map((result) => result.data as PortfolioBlock)
    : [];

  const portfolio: PortfolioDocument = {
    id: record.id,
    title: record.title,
    slug: record.slug,
    description: record.description,
    blocks: parsedBlocks,
    theme: (["atelier", "nocturne", "gallery"] as const).includes(record.theme as PortfolioDocument["theme"]) ? record.theme as PortfolioDocument["theme"] : "atelier",
    publishedAt: record.publishedAt?.toISOString() ?? null,
    updatedAt: record.updatedAt.toISOString()
  };

  return <Editor portfolio={portfolio} />;
}
