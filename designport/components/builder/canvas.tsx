"use client";

import { useDroppable } from "@dnd-kit/core";
import { SortableContext, verticalListSortingStrategy } from "@dnd-kit/sortable";

import { SortableBlock } from "@/components/builder/sortable-block";
import { cn } from "@/lib/utils";
import type { PortfolioBlock, PortfolioDocument } from "@/types/portfolio";

type CanvasProps = {
  blocks: PortfolioBlock[];
  theme: PortfolioDocument["theme"];
  selectedId: string | null;
  onSelect: (id: string) => void;
  onMove: (id: string, direction: -1 | 1) => void;
  onDuplicate: (id: string) => void;
  onDelete: (id: string) => void;
};

export function Canvas({ blocks, theme, selectedId, onSelect, onMove, onDuplicate, onDelete }: CanvasProps) {
  const { setNodeRef, isOver } = useDroppable({ id: "canvas-dropzone" });
  return (
    <section aria-labelledby="canvas-title">
      <h2 id="canvas-title" className="sr-only">Portfolio canvas</h2>
      <div
        ref={setNodeRef}
        className={cn(
          "mx-auto min-h-[42rem] w-full max-w-4xl overflow-hidden rounded-2xl border bg-[#fbf7ef] text-[#211e22] shadow-luxury transition-shadow",
          theme === "nocturne" && "bg-[#171519] text-[#f8f2e8]",
          theme === "gallery" && "bg-white text-[#151515]",
          isOver && "shadow-glow ring-2 ring-amethyst-500"
        )}
      >
        <SortableContext items={blocks.map((block) => block.id)} strategy={verticalListSortingStrategy}>
          {blocks.map((block, index) => (
            <SortableBlock
              key={block.id}
              block={block}
              index={index}
              count={blocks.length}
              selected={selectedId === block.id}
              onSelect={() => onSelect(block.id)}
              onMove={(direction) => onMove(block.id, direction)}
              onDuplicate={() => onDuplicate(block.id)}
              onDelete={() => onDelete(block.id)}
            />
          ))}
        </SortableContext>
        {blocks.length === 0 && (
          <div className="grid min-h-[42rem] place-items-center p-8 text-center">
            <div><p className="font-display text-2xl font-semibold">Your page is ready.</p><p className="mt-2 text-sm opacity-60">Drag a block here or choose one from the library.</p></div>
          </div>
        )}
      </div>
    </section>
  );
}
