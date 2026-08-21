"use client";

import { useDroppable } from "@dnd-kit/core";
import { SortableContext, verticalListSortingStrategy } from "@dnd-kit/sortable";
import { Monitor, Plus } from "lucide-react";

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
    <section aria-labelledby="canvas-title" className="studio-panel overflow-hidden rounded-[1.75rem]">
      <div className="flex min-h-14 items-center gap-3 border-b bg-surface px-4">
        <span className="grid size-8 place-items-center rounded-xl bg-ink text-canvas"><Monitor size={15} aria-hidden="true" /></span>
        <div><h2 id="canvas-title" className="text-xs font-extrabold uppercase tracking-[.1em]">Live canvas</h2><p className="text-[0.625rem] text-muted">Desktop preview · responsive output</p></div>
        <div className="ml-auto flex items-center gap-2"><span className="size-2.5 rounded-full bg-[#ff625d]" /><span className="size-2.5 rounded-full bg-[#ffc043]" /><span className="size-2.5 rounded-full bg-[#55c878]" /></div>
      </div>
      <div className="editorial-grid overflow-x-auto p-3 sm:p-5 lg:p-7">
        <div
          ref={setNodeRef}
          className={cn(
            "mx-auto min-h-[42rem] w-full max-w-4xl overflow-hidden rounded-[1.25rem] border border-black/[.15] bg-[#f3eee3] text-[#171719] shadow-[0_24px_70px_-35px_rgba(0,0,0,.6)] transition-shadow",
            theme === "nocturne" && "bg-[#15161a] text-[#f7f3ea]",
            theme === "gallery" && "bg-white text-[#111214]",
            isOver && "shadow-glow ring-2 ring-signal-500"
          )}
        >
          <SortableContext items={blocks.map((block) => block.id)} strategy={verticalListSortingStrategy}>
            {blocks.map((block, index) => (
              <SortableBlock key={block.id} block={block} index={index} count={blocks.length} selected={selectedId === block.id} onSelect={() => onSelect(block.id)} onMove={(direction) => onMove(block.id, direction)} onDuplicate={() => onDuplicate(block.id)} onDelete={() => onDelete(block.id)} />
            ))}
          </SortableContext>
          {blocks.length === 0 && (
            <div className="grid min-h-[42rem] place-items-center p-8 text-center">
              <div><span className="mx-auto grid size-12 place-items-center rounded-full bg-signal-500 text-white"><Plus size={20} aria-hidden="true" /></span><p className="mt-5 font-display text-3xl font-semibold">Your page is ready.</p><p className="mt-2 text-sm opacity-60">Drag a section here or choose one from the library.</p></div>
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
