import type { ContactContent, HeroContent, PortfolioBlock, ProjectsContent, TextContent } from "@/types/portfolio";
import { cn } from "@/lib/utils";

export function BlockRenderer({ block, primaryHeading = false, anchorId }: { block: PortfolioBlock; primaryHeading?: boolean; anchorId?: string }) {
  const className = cn(
    "px-6 py-14 sm:px-10 sm:py-20",
    block.settings.align === "center" && "text-center",
    block.settings.tone === "accent" && "bg-[#efe9ff] text-[#211b2e]",
    block.settings.tone === "contrast" && "bg-[#211e22] text-[#fbf7ef]"
  );

  switch (block.type) {
    case "hero": {
      const content = block.content as HeroContent;
      const Heading = primaryHeading ? "h1" : "h2";
      return (
        <section className={cn(className, "grid min-h-[28rem] content-center")} aria-labelledby={`${block.id}-heading`}>
          <div className={cn("max-w-3xl", block.settings.align === "center" && "mx-auto")}>
            <p className="text-xs font-bold uppercase tracking-[0.2em] opacity-70">{content.eyebrow}</p>
            <Heading id={`${block.id}-heading`} className="mt-4 font-display text-4xl font-semibold leading-[1.02] sm:text-5xl">{content.heading}</Heading>
            <p className={cn("mt-6 max-w-2xl text-base leading-7 opacity-75", block.settings.align === "center" && "mx-auto")}>{content.body}</p>
            {content.ctaLabel && (
              <a href={safeHref(content.ctaUrl)} className={cn("mt-8 inline-flex min-h-11 items-center rounded-xl px-5 py-2 text-sm font-bold", block.settings.tone === "contrast" ? "bg-[#fbf7ef] text-[#211e22]" : "bg-[#211e22] text-white")}>
                {content.ctaLabel}
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
          <div className={cn("max-w-2xl", block.settings.align === "center" && "mx-auto")}>
            <h2 id={`${block.id}-heading`} className="font-display text-3xl font-semibold">{content.heading}</h2>
            <p className="mt-5 whitespace-pre-wrap text-base leading-8 opacity-75">{content.body}</p>
          </div>
        </section>
      );
    }
    case "projects": {
      const content = block.content as ProjectsContent;
      return (
        <section id={anchorId} className={className} aria-labelledby={`${block.id}-heading`}>
          <h2 id={`${block.id}-heading`} className="font-display text-3xl font-semibold">{content.heading}</h2>
          <div className="mt-8 grid gap-4 sm:grid-cols-2">
            {content.items.map((project, index) => (
              <article key={`${project.title}-${index}`} className="rounded-2xl border border-current/15 bg-white/5 p-6 text-left">
                <span className="text-xs opacity-50">{String(index + 1).padStart(2, "0")}</span>
                <h3 className="mt-12 font-display text-xl font-semibold">{project.title}</h3>
                <p className="mt-2 text-sm leading-6 opacity-70">{project.description}</p>
              </article>
            ))}
          </div>
        </section>
      );
    }
    case "contact": {
      const content = block.content as ContactContent;
      return (
        <section className={cn(className, "py-20 sm:py-28")} aria-labelledby={`${block.id}-heading`}>
          <div className={cn("max-w-2xl", block.settings.align === "center" && "mx-auto")}>
            <h2 id={`${block.id}-heading`} className="font-display text-3xl font-semibold sm:text-4xl">{content.heading}</h2>
            <p className="mt-4 leading-7 opacity-75">{content.body}</p>
            <a className="mt-8 inline-block border-b border-current pb-1 font-bold" href={`mailto:${content.email}`}>{content.email}</a>
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
