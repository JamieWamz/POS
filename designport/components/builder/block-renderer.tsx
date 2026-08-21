import { ArrowDownRight, ArrowUpRight } from "lucide-react";

import { cn } from "@/lib/utils";
import type { ContactContent, HeroContent, PortfolioBlock, ProjectsContent, TextContent } from "@/types/portfolio";

const projectColors = ["bg-[#3b55e6] text-white", "bg-[#d8ff57] text-[#151619]", "bg-[#ff7657] text-[#151619]", "bg-[#e8ddff] text-[#151619]"];

export function BlockRenderer({ block, primaryHeading = false, anchorId }: { block: PortfolioBlock; primaryHeading?: boolean; anchorId?: string }) {
  const className = cn(
    "relative border-b border-current/10 px-6 py-16 sm:px-10 sm:py-24 lg:px-14",
    block.settings.align === "center" && "text-center",
    block.settings.tone === "accent" && "bg-[#e9e4ff] text-[#171523]",
    block.settings.tone === "contrast" && "bg-[#151619] text-[#f7f3ea]"
  );

  switch (block.type) {
    case "hero": {
      const content = block.content as HeroContent;
      const Heading = primaryHeading ? "h1" : "h2";
      return (
        <section className={cn(className, "grid min-h-[34rem] content-center overflow-hidden sm:min-h-[40rem]")} aria-labelledby={`${block.id}-heading`}>
          <div aria-hidden="true" className="absolute -right-24 top-1/2 size-72 -translate-y-1/2 rounded-full border-[4rem] border-current opacity-[.04] sm:size-96" />
          <div className={cn("relative max-w-4xl", block.settings.align === "center" && "mx-auto")}>
            <div className={cn("flex items-center gap-3", block.settings.align === "center" && "justify-center")}><span className="size-2 rounded-full bg-[#4f6cff]" /><p className="font-mono text-[0.625rem] font-bold uppercase tracking-[0.22em] opacity-[.65]">{content.eyebrow}</p></div>
            <Heading id={`${block.id}-heading`} className="mt-7 font-display text-[2.8rem] font-semibold leading-[.9] tracking-[-0.05em] sm:text-6xl lg:text-7xl">{content.heading}</Heading>
            <p className={cn("mt-7 max-w-2xl text-base leading-8 opacity-70", block.settings.align === "center" && "mx-auto")}>{content.body}</p>
            {content.ctaLabel && (
              <a href={safeHref(content.ctaUrl)} className={cn("mt-9 inline-flex min-h-11 items-center gap-2 rounded-full px-5 py-2 text-sm font-extrabold transition-transform hover:-translate-y-0.5", block.settings.tone === "contrast" ? "bg-[#d8ff57] text-[#151619]" : "bg-[#151619] text-white")}>
                {content.ctaLabel}<ArrowDownRight size={16} aria-hidden="true" />
              </a>
            )}
          </div>
        </section>
      );
    }
    case "text": {
      const content = block.content as TextContent;
      return (
        <section className={className} aria-labelledby={`${block.id}-heading`}>
          <div className={cn("grid max-w-5xl gap-8 sm:grid-cols-[.7fr_1.3fr] sm:gap-12", block.settings.align === "center" && "mx-auto sm:block")}>
            <div><p className="font-mono text-[0.625rem] font-bold uppercase tracking-[.2em] opacity-50">About / Story</p><h2 id={`${block.id}-heading`} className="mt-4 font-display text-4xl font-semibold leading-none tracking-[-0.035em] sm:text-5xl">{content.heading}</h2></div>
            <p className={cn("whitespace-pre-wrap text-base leading-8 opacity-70 sm:pt-7", block.settings.align === "center" && "mx-auto mt-6 max-w-2xl sm:pt-0")}>{content.body}</p>
          </div>
        </section>
      );
    }
    case "projects": {
      const content = block.content as ProjectsContent;
      return (
        <section id={anchorId} className={className} aria-labelledby={`${block.id}-heading`}>
          <div className={cn("flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between", block.settings.align === "center" && "items-center sm:flex-col sm:items-center")}>
            <div><p className="font-mono text-[0.625rem] font-bold uppercase tracking-[.2em] opacity-50">Selected work</p><h2 id={`${block.id}-heading`} className="mt-4 font-display text-4xl font-semibold tracking-[-0.035em] sm:text-5xl">{content.heading}</h2></div>
            <p className="font-mono text-[0.625rem] font-bold uppercase tracking-[.16em] opacity-50">{String(content.items.length).padStart(2, "0")} projects</p>
          </div>
          <div className="mt-10 grid gap-4 sm:grid-cols-2">
            {content.items.map((project, index) => (
              <article key={`${project.title}-${index}`} className="group overflow-hidden rounded-[1.5rem] border border-current/[.15] bg-white/[.04] text-left">
                <div className={cn("relative aspect-[16/9] overflow-hidden p-5", projectColors[index % projectColors.length])}>
                  <div aria-hidden="true" className="absolute inset-0 opacity-20 [background-image:linear-gradient(currentColor_1px,transparent_1px),linear-gradient(90deg,currentColor_1px,transparent_1px)] [background-size:32px_32px]" />
                  <span className="relative font-mono text-[0.625rem] font-bold uppercase tracking-[.18em] opacity-70">Case study / {String(index + 1).padStart(2, "0")}</span>
                  <ArrowUpRight className="absolute right-5 top-5 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1" size={20} aria-hidden="true" />
                  <div aria-hidden="true" className="absolute -bottom-12 -right-8 size-44 rotate-12 rounded-[2.5rem] border-[2rem] border-current opacity-25" />
                </div>
                <div className="p-6 sm:p-7"><h3 className="font-display text-2xl font-semibold tracking-[-0.025em]">{project.title}</h3><p className="mt-3 text-sm leading-6 opacity-[.65]">{project.description}</p></div>
              </article>
            ))}
          </div>
        </section>
      );
    }
    case "contact": {
      const content = block.content as ContactContent;
      return (
        <section className={cn(className, "overflow-hidden py-24 sm:py-32")} aria-labelledby={`${block.id}-heading`}>
          <div aria-hidden="true" className="absolute -bottom-20 -right-12 font-display text-[18rem] italic leading-none opacity-[.035]">@</div>
          <div className={cn("relative max-w-4xl", block.settings.align === "center" && "mx-auto")}>
            <p className="font-mono text-[0.625rem] font-bold uppercase tracking-[.2em] opacity-50">Start a conversation</p>
            <h2 id={`${block.id}-heading`} className="mt-5 font-display text-5xl font-semibold leading-[.92] tracking-[-0.045em] sm:text-6xl">{content.heading}</h2>
            <p className={cn("mt-6 max-w-2xl text-base leading-8 opacity-70", block.settings.align === "center" && "mx-auto")}>{content.body}</p>
            <a className="mt-10 inline-flex items-center gap-2 border-b-2 border-current pb-1 text-sm font-extrabold transition-opacity hover:opacity-60 sm:text-base" href={`mailto:${content.email}`}>{content.email}<ArrowUpRight size={17} aria-hidden="true" /></a>
          </div>
        </section>
      );
    }
  }
}

function safeHref(value: string) {
  if (value.startsWith("#") || /^\/(?!\/)/.test(value) || /^https?:\/\//i.test(value)) return value;
  return "#";
}
