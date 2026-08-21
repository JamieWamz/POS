"use client";

import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { ArrowDown, ArrowUp, Copy, GripVertical, Trash2 } from "lucide-react";

import { BlockRenderer } from "@/components/builder/block-renderer";
import { cn } from "@/lib/utils";
import type { PortfolioBlock } from "@/types/portfolio";

type Props = {
  block: PortfolioBlock;
  selected: boolean;
  index: number;
  count: number;
  onSelect: () => void;
  onMove: (direction: -1 | 1) => void;
  onDuplicate: () => void;
  onDelete: () => void;
};

export function SortableBlock({ block, selected, index, count, onSelect, onMove, onDuplicate, onDelete }: Props) {
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({
    id: block.id,
    data: { source: "canvas", type: block.type }
  });

  return (
    <article
      ref={setNodeRef}
      style={{ transform: CSS.Transform.toString(transform), transition }}
      className={cn("group relative border-2 border-transparent transition-[border-color,box-shadow]", selected && "z-10 border-signal-500 shadow-[inset_0_0_0_1px_rgba(255,255,255,.45)]", isDragging && "z-20 opacity-60 shadow-2xl")}
      onClick={onSelect}
      aria-label={`${block.type} block, position ${index + 1} of ${count}${selected ? ", selected" : ""}`}
    >
      <div className={cn("absolute right-3 top-3 z-20 flex flex-wrap items-center rounded-full border bg-elevated/95 p-1 text-ink shadow-xl backdrop-blur transition-all", selected ? "translate-y-0 opacity-100" : "-translate-y-1 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100")}>
        <button className="grid size-10 cursor-grab place-items-center rounded-lg hover:bg-canvas active:cursor-grabbing" aria-label={`Drag ${block.type} block`} {...attributes} {...listeners}>
          <GripVertical size={17} aria-hidden="true" />
        </button>
        <button className="grid size-10 place-items-center rounded-lg hover:bg-canvas disabled:opacity-30" onClick={(event) => { event.stopPropagation(); onMove(-1); }} disabled={index === 0} aria-label="Move block up"><ArrowUp size={16} aria-hidden="true" /></button>
        <button className="grid size-10 place-items-center rounded-lg hover:bg-canvas disabled:opacity-30" onClick={(event) => { event.stopPropagation(); onMove(1); }} disabled={index === count - 1} aria-label="Move block down"><ArrowDown size={16} aria-hidden="true" /></button>
        <button className="grid size-10 place-items-center rounded-lg hover:bg-canvas" onClick={(event) => { event.stopPropagation(); onDuplicate(); }} aria-label="Duplicate block"><Copy size={16} aria-hidden="true" /></button>
        <button className="grid size-10 place-items-center rounded-lg text-red-700 hover:bg-red-500/10 dark:text-red-300" onClick={(event) => { event.stopPropagation(); onDelete(); }} aria-label="Delete block"><Trash2 size={16} aria-hidden="true" /></button>
      </div>
      <BlockRenderer block={block} />
    </article>
  );
}
