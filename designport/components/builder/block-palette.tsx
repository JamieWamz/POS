"use client";

import { useDraggable } from "@dnd-kit/core";
import { Contact, GripVertical, LayoutTemplate, PanelsTopLeft, Plus, Type } from "lucide-react";

import { blockCatalog } from "@/lib/blocks";
import { cn } from "@/lib/utils";
import type { BlockType } from "@/types/portfolio";

const icons = { hero: LayoutTemplate, text: Type, projects: PanelsTopLeft, contact: Contact };

export function BlockPalette({ onAdd }: { onAdd: (type: BlockType) => void }) {
  return (
    <aside className="studio-panel rounded-[1.5rem] p-4 lg:sticky lg:top-36 lg:max-h-[calc(100vh-10rem)]" aria-labelledby="palette-title">
      <div className="border-b pb-4">
        <div className="flex items-center justify-between"><p className="index-label">Library</p><span className="grid size-7 place-items-center rounded-full bg-acid-400 text-[10px] font-black text-neutral-950">04</span></div>
        <h2 id="palette-title" className="mt-3 font-display text-2xl font-semibold leading-none">Add a section</h2>
        <p className="mt-2 text-xs leading-5 text-muted">Click to add or use the handle to drag.</p>
      </div>
      <div className="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
        {blockCatalog.map((item, index) => <PaletteItem key={item.type} {...item} index={index} onAdd={onAdd} />)}
      </div>
    </aside>
  );
}

function PaletteItem({ type, label, description, index, onAdd }: { type: BlockType; label: string; description: string; index: number; onAdd: (type: BlockType) => void }) {
  const { attributes, listeners, setNodeRef, isDragging } = useDraggable({ id: `palette-${type}`, data: { source: "palette", type } });
  const Icon = icons[type];

  return (
    <div ref={setNodeRef} className={cn("group flex min-h-[5.25rem] w-full items-center rounded-2xl border bg-elevated p-1.5 transition duration-160 hover:-translate-y-0.5 hover:border-signal-500 hover:shadow-md", isDragging && "opacity-40")}>
      <button type="button" onClick={() => onAdd(type)} className="flex min-h-[4.25rem] min-w-0 flex-1 items-center gap-3 rounded-xl p-2 text-left" aria-label={`Add ${label} block. ${description}`}>
        <span className="relative grid size-10 shrink-0 place-items-center rounded-xl bg-ink text-canvas"><Icon size={17} aria-hidden="true" /><span className="absolute -right-1 -top-1 grid size-4 place-items-center rounded-full bg-acid-400 text-neutral-950"><Plus size={9} strokeWidth={3} /></span></span>
        <span className="min-w-0 flex-1"><span className="block text-[0.6875rem] font-bold uppercase tracking-[.1em] text-muted">0{index + 1}</span><span className="mt-0.5 block text-sm font-extrabold">{label}</span></span>
      </button>
      <button type="button" className="grid size-11 shrink-0 cursor-grab place-items-center rounded-xl text-muted hover:bg-canvas active:cursor-grabbing" aria-label={`Drag ${label} block onto canvas`} {...listeners} {...attributes}><GripVertical size={16} aria-hidden="true" /></button>
    </div>
  );
}
