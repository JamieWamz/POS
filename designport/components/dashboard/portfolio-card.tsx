"use client";

import { ExternalLink, LoaderCircle, Pencil, Trash2 } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { buttonVariants } from "@/components/ui/button-variants";
import { formatRelativeDate } from "@/lib/utils";

type PortfolioCardProps = {
  portfolio: {
    id: string;
    title: string;
    slug: string;
    description: string;
    publishedAt: Date | null;
    updatedAt: Date;
  };
};

export function PortfolioCard({ portfolio }: PortfolioCardProps) {
  const router = useRouter();
  const [pending, setPending] = useState(false);
  const [error, setError] = useState("");

  async function removePortfolio() {
    if (!window.confirm(`Delete “${portfolio.title}”? This cannot be undone.`)) return;
    try {
      setPending(true);
      setError("");
      const response = await fetch(`/api/portfolios/${portfolio.id}`, { method: "DELETE" });
      if (!response.ok) {
        const data = (await response.json()) as { error?: string };
        throw new Error(data.error ?? "Deletion failed.");
      }
      router.refresh();
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "Deletion failed.");
      setPending(false);
    }
  }

  return (
    <article className="group flex min-h-64 flex-col rounded-3xl border bg-surface p-6 shadow-luxury transition duration-240 hover:-translate-y-1 hover:border-champagne-400">
      <div className="flex items-start justify-between gap-4">
        <span className={`rounded-full px-2.5 py-1 text-xs font-bold ${portfolio.publishedAt ? "bg-emerald-700 text-white" : "bg-line/60 text-muted"}`}>
          {portfolio.publishedAt ? "Published" : "Draft"}
        </span>
        <span className="text-xs text-muted">Updated {formatRelativeDate(portfolio.updatedAt)}</span>
      </div>
      <h2 className="mt-8 font-display text-2xl font-semibold">{portfolio.title}</h2>
      <p className="mt-2 line-clamp-2 text-sm leading-6 text-muted">{portfolio.description || "A new story waiting to be shaped."}</p>
      <div className="mt-auto flex flex-wrap items-center gap-2 pt-6">
        <Link href={`/builder/${portfolio.id}`} className={buttonVariants("secondary")}>
          <Pencil size={16} aria-hidden="true" /> Edit
        </Link>
        {portfolio.publishedAt && (
          <Link href={`/p/${portfolio.slug}`} target="_blank" className={buttonVariants("ghost")}>
            <ExternalLink size={16} aria-hidden="true" /> View <span className="sr-only">{portfolio.title}</span>
          </Link>
        )}
        <Button variant="ghost" className="ml-auto px-3 text-red-700 dark:text-red-300" onClick={removePortfolio} disabled={pending} aria-label={`Delete ${portfolio.title}`}>
          {pending ? <LoaderCircle className="animate-spin motion-reduce:animate-none" size={17} aria-hidden="true" /> : <Trash2 size={17} aria-hidden="true" />}
        </Button>
      </div>
      {error && <p role="alert" className="mt-3 text-sm text-red-700 dark:text-red-300">{error}</p>}
    </article>
  );
}
