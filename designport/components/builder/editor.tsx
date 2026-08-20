"use client";

import {
  closestCenter,
  DndContext,
  type DragEndEvent,
  KeyboardSensor,
  PointerSensor,
  useSensor,
  useSensors
} from "@dnd-kit/core";
import { sortableKeyboardCoordinates, arrayMove } from "@dnd-kit/sortable";
import { ExternalLink, LoaderCircle, Redo2, Save, Undo2, UploadCloud } from "lucide-react";
import Link from "next/link";
import { useCallback, useEffect, useRef, useState } from "react";

import { BlockPalette } from "@/components/builder/block-palette";
import { Canvas } from "@/components/builder/canvas";
import { Inspector } from "@/components/builder/inspector";
import { Button } from "@/components/ui/button";
import { buttonVariants } from "@/components/ui/button-variants";
import { createBlock } from "@/lib/blocks";
import { cn } from "@/lib/utils";
import type { BlockType, PortfolioBlock, PortfolioDocument } from "@/types/portfolio";

type SaveStatus = "idle" | "unsaved" | "saving" | "saved" | "error";

export function Editor({ portfolio }: { portfolio: PortfolioDocument }) {
  const [blocks, setBlocks] = useState(portfolio.blocks);
  const [past, setPast] = useState<PortfolioBlock[][]>([]);
  const [future, setFuture] = useState<PortfolioBlock[][]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(portfolio.blocks[0]?.id ?? null);
  const [title, setTitle] = useState(portfolio.title);
  const [slug, setSlug] = useState(portfolio.slug);
  const [description, setDescription] = useState(portfolio.description);
  const [theme, setTheme] = useState(portfolio.theme);
  const [publishedAt, setPublishedAt] = useState(portfolio.publishedAt);
  const [saveStatus, setSaveStatus] = useState<SaveStatus>("idle");
  const [saveError, setSaveError] = useState("");
  const [announcement, setAnnouncement] = useState("");
  const [publishing, setPublishing] = useState(false);
  const dirtyRef = useRef(false);
  const revisionRef = useRef(0);
  const requestRef = useRef<AbortController | null>(null);

  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 6 } }),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates })
  );

  const markDirty = useCallback(() => {
    dirtyRef.current = true;
    revisionRef.current += 1;
    setSaveStatus("unsaved");
  }, []);

  const commit = useCallback((next: PortfolioBlock[] | ((current: PortfolioBlock[]) => PortfolioBlock[])) => {
    setBlocks((current) => {
      const resolved = typeof next === "function" ? next(current) : next;
      if (resolved === current) return current;
      setPast((history) => [...history.slice(-49), current]);
      setFuture([]);
      return resolved;
    });
    markDirty();
  }, [markDirty]);

  const saveDraft = useCallback(async () => {
    requestRef.current?.abort();
    const controller = new AbortController();
    requestRef.current = controller;
    const savingRevision = revisionRef.current;
    setSaveStatus("saving");
    setSaveError("");
    try {
      const response = await fetch(`/api/portfolios/${portfolio.id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, slug, description, blocks, theme }),
        signal: controller.signal
      });
      const data = (await response.json()) as { error?: string };
      if (!response.ok) throw new Error(data.error ?? "Your changes could not be saved.");
      if (revisionRef.current === savingRevision) {
        dirtyRef.current = false;
        setSaveStatus("saved");
      }
      return true;
    } catch (caught) {
      if (caught instanceof DOMException && caught.name === "AbortError") return false;
      setSaveStatus("error");
      setSaveError(caught instanceof Error ? caught.message : "Your changes could not be saved.");
      return false;
    }
  }, [blocks, description, portfolio.id, slug, theme, title]);

  useEffect(() => {
    if (!dirtyRef.current) return;
    const timer = window.setTimeout(() => { void saveDraft(); }, 900);
    return () => window.clearTimeout(timer);
  }, [blocks, description, saveDraft, slug, theme, title]);

  useEffect(() => () => requestRef.current?.abort(), []);

  useEffect(() => {
    function warnBeforeLeaving(event: BeforeUnloadEvent) {
      if (!dirtyRef.current) return;
      event.preventDefault();
    }
    window.addEventListener("beforeunload", warnBeforeLeaving);
    return () => window.removeEventListener("beforeunload", warnBeforeLeaving);
  }, []);

  function changeMetadata(setter: (value: string) => void, value: string) {
    setter(value);
    markDirty();
  }

  function addBlock(type: BlockType, atIndex = blocks.length) {
    const block = createBlock(type);
    commit((current) => {
      const next = [...current];
      next.splice(Math.min(atIndex, current.length), 0, block);
      return next;
    });
    setSelectedId(block.id);
    setAnnouncement(`${type} block added at position ${atIndex + 1}.`);
  }

  function handleDragEnd(event: DragEndEvent) {
    const { active, over } = event;
    if (!over) return;
    const source = active.data.current?.source;
    if (source === "palette") {
      const targetIndex = blocks.findIndex((block) => block.id === over.id);
      addBlock(active.data.current?.type as BlockType, targetIndex < 0 ? blocks.length : targetIndex);
      return;
    }
    if (active.id === over.id) return;
    const oldIndex = blocks.findIndex((block) => block.id === active.id);
    const newIndex = over.id === "canvas-dropzone"
      ? blocks.length - 1
      : blocks.findIndex((block) => block.id === over.id);
    if (oldIndex < 0 || newIndex < 0 || oldIndex === newIndex) return;
    commit(arrayMove(blocks, oldIndex, newIndex));
    setAnnouncement(`${active.data.current?.type ?? "Block"} moved to position ${newIndex + 1}.`);
  }

  function moveBlock(id: string, direction: -1 | 1) {
    const index = blocks.findIndex((block) => block.id === id);
    const nextIndex = index + direction;
    if (index < 0 || nextIndex < 0 || nextIndex >= blocks.length) return;
    commit(arrayMove(blocks, index, nextIndex));
    setAnnouncement(`Block moved to position ${nextIndex + 1}.`);
  }

  function duplicateBlock(id: string) {
    const index = blocks.findIndex((block) => block.id === id);
    if (index < 0) return;
    const copy = { ...structuredClone(blocks[index]), id: crypto.randomUUID() };
    commit((current) => [...current.slice(0, index + 1), copy, ...current.slice(index + 1)]);
    setSelectedId(copy.id);
    setAnnouncement(`Block duplicated at position ${index + 2}.`);
  }

  function deleteBlock(id: string) {
    const index = blocks.findIndex((block) => block.id === id);
    commit((current) => current.filter((block) => block.id !== id));
    if (selectedId === id) setSelectedId(blocks[index + 1]?.id ?? blocks[index - 1]?.id ?? null);
    setAnnouncement("Block deleted. Use Undo to restore it.");
  }

  function undo() {
    const previous = past[past.length - 1];
    if (!previous) return;
    setPast(past.slice(0, -1));
    setFuture((history) => [blocks, ...history].slice(0, 50));
    setBlocks(previous);
    setSelectedId((current) => previous.some((block) => block.id === current) ? current : previous[0]?.id ?? null);
    markDirty();
    setAnnouncement("Last canvas change undone.");
  }

  function redo() {
    const next = future[0];
    if (!next) return;
    setFuture(future.slice(1));
    setPast((history) => [...history.slice(-49), blocks]);
    setBlocks(next);
    setSelectedId((current) => next.some((block) => block.id === current) ? current : next[0]?.id ?? null);
    markDirty();
    setAnnouncement("Canvas change restored.");
  }

  async function togglePublish() {
    try {
      setPublishing(true);
      setSaveError("");
      if (dirtyRef.current && !(await saveDraft())) throw new Error("Resolve the save error before publishing.");
      const shouldPublish = !publishedAt;
      const response = await fetch(`/api/portfolios/${portfolio.id}/publish`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ published: shouldPublish })
      });
      const data = (await response.json()) as { portfolio?: { publishedAt: string | null }; error?: string };
      if (!response.ok || !data.portfolio) throw new Error(data.error ?? "Publication failed.");
      setPublishedAt(data.portfolio.publishedAt);
      setSaveStatus("saved");
      setAnnouncement(shouldPublish ? "Portfolio published." : "Portfolio returned to draft.");
    } catch (caught) {
      setSaveError(caught instanceof Error ? caught.message : "Publication failed.");
      setSaveStatus("error");
    } finally {
      setPublishing(false);
    }
  }

  const selectedBlock = blocks.find((block) => block.id === selectedId) ?? null;

  return (
    <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
      <main id="main-content" tabIndex={-1} className="min-h-[calc(100vh-4rem)] bg-canvas">
        <h1 className="sr-only">Edit {title || "untitled portfolio"}</h1>
        <div className="sticky top-16 z-40 border-b bg-surface/95 px-4 py-3 backdrop-blur-xl sm:px-6 lg:px-8">
          <div className="mx-auto flex max-w-[1536px] flex-wrap items-center gap-2">
            <div className="mr-auto min-w-0">
              <p className="truncate text-sm font-bold">{title || "Untitled portfolio"}</p>
              <SaveIndicator status={saveStatus} error={saveError} />
            </div>
            <Button variant="ghost" className="size-11 px-0" onClick={undo} disabled={past.length === 0} aria-label="Undo canvas change"><Undo2 size={17} aria-hidden="true" /></Button>
            <Button variant="ghost" className="size-11 px-0" onClick={redo} disabled={future.length === 0} aria-label="Redo canvas change"><Redo2 size={17} aria-hidden="true" /></Button>
            {publishedAt && <Link href={`/p/${slug}`} target="_blank" className={buttonVariants("secondary")}><ExternalLink size={16} aria-hidden="true" /><span className="hidden sm:inline">Preview</span></Link>}
            <Button onClick={togglePublish} disabled={publishing} variant={publishedAt ? "secondary" : "primary"}>
              {publishing ? <LoaderCircle className="animate-spin motion-reduce:animate-none" size={17} aria-hidden="true" /> : <UploadCloud size={17} aria-hidden="true" />}
              {publishedAt ? "Unpublish" : "Publish"}
            </Button>
          </div>
        </div>

        <div className="mx-auto grid max-w-[1536px] gap-5 px-4 py-6 sm:px-6 lg:grid-cols-[15rem_minmax(0,1fr)_18rem] lg:items-start lg:px-8 xl:grid-cols-[17rem_minmax(0,1fr)_20rem]">
          <BlockPalette onAdd={addBlock} />
          <div className="min-w-0">
            <section className="mb-5 rounded-2xl border bg-surface p-4" aria-labelledby="page-settings-title">
              <h2 id="page-settings-title" className="font-display text-lg font-semibold">Page settings</h2>
              <div className="mt-4 grid gap-3 sm:grid-cols-2">
                <div><label htmlFor="portfolio-title" className="field-label">Portfolio title</label><input id="portfolio-title" className="field" maxLength={80} value={title} onChange={(event) => changeMetadata(setTitle, event.target.value)} /></div>
                <div><label htmlFor="portfolio-slug" className="field-label">Public URL</label><div className="flex items-center rounded-xl border bg-elevated pl-3 text-xs text-muted focus-within:ring-2 focus-within:ring-amethyst-500"><span>/p/</span><input id="portfolio-slug" className="min-h-11 min-w-0 flex-1 bg-transparent px-1 py-2 text-sm text-ink outline-none" maxLength={48} value={slug} onChange={(event) => changeMetadata(setSlug, event.target.value.toLowerCase())} aria-describedby="slug-hint" /></div><p id="slug-hint" className="mt-1 text-xs text-muted">Lowercase letters, numbers and hyphens.</p></div>
                <div className="sm:col-span-2"><label htmlFor="portfolio-description" className="field-label">Search and share description</label><textarea id="portfolio-description" className="field resize-y" rows={2} maxLength={180} value={description} onChange={(event) => changeMetadata(setDescription, event.target.value)} /></div>
                <div><label htmlFor="portfolio-theme" className="field-label">Portfolio theme</label><select id="portfolio-theme" className="field" value={theme} onChange={(event) => { setTheme(event.target.value as PortfolioDocument["theme"]); markDirty(); }}><option value="atelier">Atelier</option><option value="nocturne">Nocturne</option><option value="gallery">Gallery</option></select></div>
              </div>
            </section>
            <Canvas blocks={blocks} theme={theme} selectedId={selectedId} onSelect={setSelectedId} onMove={moveBlock} onDuplicate={duplicateBlock} onDelete={deleteBlock} />
          </div>
          <Inspector block={selectedBlock} onChange={(updated) => commit((current) => current.map((block) => block.id === updated.id ? updated : block))} />
        </div>
        <div className="sr-only" aria-live="polite" aria-atomic="true">{announcement}</div>
      </main>
    </DndContext>
  );
}

function SaveIndicator({ status, error }: { status: SaveStatus; error: string }) {
  return (
    <div className={cn("flex items-center gap-1.5 text-xs text-muted", status === "error" && "text-red-700 dark:text-red-300")} role={status === "error" ? "alert" : "status"}>
      {status === "saving" && <LoaderCircle className="animate-spin motion-reduce:animate-none" size={12} aria-hidden="true" />}
      {(status === "saved" || status === "idle") && <Save size={12} aria-hidden="true" />}
      {status === "idle" ? "Ready" : status === "unsaved" ? "Unsaved changes" : status === "saving" ? "Saving…" : status === "saved" ? "All changes saved" : error || "Save failed"}
    </div>
  );
}
