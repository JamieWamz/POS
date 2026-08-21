import type { Metadata } from "next";
import { notFound } from "next/navigation";

import { PortfolioRenderer } from "@/components/builder/portfolio-renderer";
import { Logo } from "@/components/ui/logo";
import { prisma } from "@/lib/prisma";
import { portfolioBlockSchema } from "@/lib/validation";
import type { PortfolioBlock, PortfolioDocument } from "@/types/portfolio";

type Props = { params: { slug: string } };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const portfolio = await prisma.portfolio.findFirst({
    where: { slug: params.slug, publishedAt: { not: null } },
    select: { title: true, description: true, slug: true }
  });
  if (!portfolio) return { title: "Portfolio not found", robots: { index: false } };
  return {
    title: portfolio.title,
    description: portfolio.description || `Explore ${portfolio.title}.`,
    alternates: { canonical: `/p/${portfolio.slug}` },
    openGraph: { title: portfolio.title, description: portfolio.description || `Explore ${portfolio.title}.`, type: "website" }
  };
}

export default async function PublicPortfolioPage({ params }: Props) {
  const record = await prisma.portfolio.findFirst({
    where: { slug: params.slug, publishedAt: { not: null } },
    select: { title: true, blocks: true, theme: true, updatedAt: true }
  });
  if (!record) notFound();

  const blocks = Array.isArray(record.blocks)
    ? record.blocks.map((block) => portfolioBlockSchema.safeParse(block)).filter((result) => result.success).map((result) => result.data as PortfolioBlock)
    : [];
  const theme = (["atelier", "nocturne", "gallery"] as const).includes(record.theme as PortfolioDocument["theme"])
    ? record.theme as PortfolioDocument["theme"]
    : "atelier";

  return (
    <main id="main-content" tabIndex={-1}>
      <PortfolioRenderer blocks={blocks} theme={theme} publicPage title={record.title} />
      <footer className="flex flex-col items-center justify-between gap-4 border-t bg-ink px-6 py-7 text-xs font-semibold text-white/50 sm:flex-row lg:px-10">
        <span>Last updated <time className="text-white" dateTime={record.updatedAt.toISOString()}>{new Intl.DateTimeFormat("en", { dateStyle: "medium" }).format(record.updatedAt)}</time></span>
        <Logo className="text-xs text-white [&>span:first-child]:bg-white [&>span:first-child]:text-neutral-950" />
      </footer>
    </main>
  );
}
