"use client";

import { ArrowUpRight, ExternalLink, LoaderCircle, Pencil, Trash2 } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { buttonVariants } from "@/components/ui/button-variants";
import { cn, formatRelativeDate } from "@/lib/utils";

type PortfolioCardProps = {
  index: number;
  portfolio: {
    id: string;
    title: string;
    slug: string;
    description: string;
    theme: string;
    publishedAt: Date | null;
    updatedAt: Date;
  };
};

export function PortfolioCard({ portfolio, index }: PortfolioCardProps) {
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
    <article className="group overflow-hidden rounded-[1.75rem] border bg-surface shadow-luxury transition duration-240 hover:-translate-y-1 hover:border-ink/30">
      <div className={cn("relative aspect-[16/10] overflow-hidden border-b bg-[#ece5d8] p-5 text-[#171719]", portfolio.theme === "nocturne" && "bg-[#17181d] text-white", portfolio.theme === "gallery" && "bg-white text-neutral-950")}>
        <div aria-hidden="true" className="absolute inset-0 opacity-30 [background-image:linear-gradient(currentColor_1px,transparent_1px),linear-gradient(90deg,currentColor_1px,transparent_1px)] [background-size:36px_36px]" />
        <div className="relative flex h-full flex-col rounded-2xl border border-current/[.15] bg-current/[.03] p-5">
          <div className="flex items-center justify-between"><span className="font-mono text-[9px] font-bold uppercase tracking-[.18em] opacity-[.55]">Portfolio / {String(index + 1).padStart(2, "0")}</span><ArrowUpRight size={16} className="transition-transform group-hover:translate-x-1 group-hover:-translate-y-1" /></div>
          <p className="mt-auto max-w-xs font-display text-3xl font-semibold leading-[.9] tracking-[-0.035em]">{portfolio.title}</p>
          <div className="mt-4 h-2 w-16 rounded-full bg-signal-500 group-hover:w-24 transition-[width] duration-400" />
        </div>
      </div>
      <div className="p-5 sm:p-6">
        <div className="flex items-center justify-between gap-4">
          <span className={cn("rounded-full px-2.5 py-1 text-[0.625rem] font-extrabold uppercase tracking-[.1em]", portfolio.publishedAt ? "bg-emerald-700 text-white" : "bg-line/60 text-muted")}>
            {portfolio.publishedAt ? "Published" : "Draft"}
          </span>
          <span className="text-[0.6875rem] font-semibold text-muted">Updated {formatRelativeDate(portfolio.updatedAt)}</span>
        </div>
        <p className="mt-4 line-clamp-2 min-h-12 text-sm leading-6 text-muted">{portfolio.description || "A new story waiting to be shaped."}</p>
        <div className="mt-5 flex flex-wrap items-center gap-1 border-t pt-4">
          <Link href={`/builder/${portfolio.id}`} className={buttonVariants("secondary")}><Pencil size={15} aria-hidden="true" /> Edit</Link>
          {portfolio.publishedAt && <Link href={`/p/${portfolio.slug}`} target="_blank" className={buttonVariants("ghost")}><ExternalLink size={15} aria-hidden="true" /> View <span className="sr-only">{portfolio.title}</span></Link>}
          <Button variant="ghost" className="ml-auto size-11 px-0 text-red-700 dark:text-red-300" onClick={removePortfolio} disabled={pending} aria-label={`Delete ${portfolio.title}`}>
            {pending ? <LoaderCircle className="animate-spin motion-reduce:animate-none" size={17} aria-hidden="true" /> : <Trash2 size={17} aria-hidden="true" />}
          </Button>
        </div>
        {error && <p role="alert" className="mt-3 text-sm text-red-700 dark:text-red-300">{error}</p>}
      </div>
    </article>
  );
}
