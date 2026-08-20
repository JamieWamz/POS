import { BlockRenderer } from "@/components/builder/block-renderer";
import { cn } from "@/lib/utils";
import type { PortfolioBlock, PortfolioDocument } from "@/types/portfolio";

export function PortfolioRenderer({ blocks, theme, publicPage = false, title = "Portfolio" }: { blocks: PortfolioBlock[]; theme: PortfolioDocument["theme"]; publicPage?: boolean; title?: string }) {
  const firstHeroIndex = blocks.findIndex((block) => block.type === "hero");
  const firstProjectsIndex = blocks.findIndex((block) => block.type === "projects");
  return (
    <div
      className={cn(
        "overflow-hidden bg-[#fbf7ef] text-[#211e22]",
        theme === "nocturne" && "bg-[#171519] text-[#f8f2e8]",
        theme === "gallery" && "bg-white text-[#151515]"
      )}
    >
      {publicPage && firstHeroIndex < 0 && <h1 className="sr-only">{title}</h1>}
      {blocks.map((block, index) => <BlockRenderer key={block.id} block={block} primaryHeading={publicPage && index === firstHeroIndex} anchorId={index === firstProjectsIndex ? "projects" : undefined} />)}
    </div>
  );
}
