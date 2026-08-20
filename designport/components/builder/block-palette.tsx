"use client";

import { useDraggable } from "@dnd-kit/core";
import { Contact, GripVertical, LayoutTemplate, PanelsTopLeft, Type } from "lucide-react";

import { blockCatalog } from "@/lib/blocks";
import { cn } from "@/lib/utils";
import type { BlockType } from "@/types/portfolio";

const icons = { hero: LayoutTemplate, text: Type, projects: PanelsTopLeft, contact: Contact };

export function BlockPalette({ onAdd }: { onAdd: (type: BlockType) => void }) {
  return (
    <aside className="rounded-2xl border bg-surface p-4 lg:sticky lg:top-20 lg:max-h-[calc(100vh-6rem)]" aria-labelledby="palette-title">
      <p className="eyebrow">Add content</p>
      <h2 id="palette-title" className="mt-2 font-display text-xl font-semibold">Block library</h2>
      <p className="mt-2 text-xs leading-5 text-muted">Drag onto the page, or press Enter to append.</p>
      <div className="mt-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
        {blockCatalog.map((item) => <PaletteItem key={item.type} {...item} onAdd={onAdd} />)}
      </div>
    </aside>
  );
}

function PaletteItem({ type, label, description, onAdd }: { type: BlockType; label: string; description: string; onAdd: (type: BlockType) => void }) {
  const { attributes, listeners, setNodeRef, isDragging } = useDraggable({
    id: `palette-${type}`,
    data: { source: "palette", type }
  });
  const Icon = icons[type];

  return (
    <div ref={setNodeRef} className={cn("flex min-h-20 w-full items-center rounded-xl border bg-elevated p-1 transition duration-160 hover:border-amethyst-500 hover:shadow-sm", isDragging && "opacity-40")}>
      <button type="button" onClick={() => onAdd(type)} className="flex min-h-[4.5rem] min-w-0 flex-1 items-center gap-3 rounded-lg p-2 text-left" aria-label={`Add ${label} block. ${description}`}>
        <span className="grid size-10 shrink-0 place-items-center rounded-xl bg-canvas text-amethyst-700 dark:text-amethyst-400"><Icon size={18} aria-hidden="true" /></span>
        <span className="min-w-0 flex-1"><span className="block text-sm font-bold">{label}</span><span className="mt-0.5 block text-xs leading-4 text-muted">{description}</span></span>
      </button>
      <button type="button" className="grid size-11 shrink-0 cursor-grab place-items-center rounded-lg text-muted hover:bg-canvas active:cursor-grabbing" aria-label={`Drag ${label} block onto canvas`} {...listeners} {...attributes}>
        <GripVertical size={16} aria-hidden="true" />
      </button>
    </div>
  );
}
