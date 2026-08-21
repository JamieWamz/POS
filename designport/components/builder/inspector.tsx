"use client";

import { Plus, Trash2 } from "lucide-react";

import { Button } from "@/components/ui/button";
import type { ContactContent, HeroContent, PortfolioBlock, ProjectsContent, TextContent } from "@/types/portfolio";

export function Inspector({ block, onChange }: { block: PortfolioBlock | null; onChange: (block: PortfolioBlock) => void }) {
  if (!block) {
    return (
      <aside className="studio-panel rounded-[1.5rem] p-5 lg:sticky lg:top-36" aria-labelledby="inspector-title">
        <p className="index-label">Properties</p>
        <h2 id="inspector-title" className="mt-3 font-display text-2xl font-semibold">Select a section</h2>
        <p className="mt-3 text-sm leading-6 text-muted">Choose a section on the canvas to edit its copy, alignment and tone.</p>
      </aside>
    );
  }

  function changeContent(content: PortfolioBlock["content"]) {
    onChange({ ...block!, content });
  }

  return (
    <aside className="studio-panel rounded-[1.5rem] p-5 lg:sticky lg:top-36 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto" aria-labelledby="inspector-title">
      <div className="border-b pb-4"><div className="flex items-center justify-between"><p className="index-label">Properties</p><span className="rounded-full bg-signal-500/10 px-2.5 py-1 text-[0.625rem] font-extrabold uppercase tracking-wider text-signal-700 dark:text-signal-400">Selected</span></div><h2 id="inspector-title" className="mt-3 font-display text-2xl font-semibold capitalize">{block.type} section</h2></div>
      <div className="mt-5 space-y-5">
        {block.type === "hero" && <HeroFields content={block.content as HeroContent} onChange={changeContent} />}
        {block.type === "text" && <TextFields content={block.content as TextContent} onChange={changeContent} />}
        {block.type === "projects" && <ProjectFields content={block.content as ProjectsContent} onChange={changeContent} />}
        {block.type === "contact" && <ContactFields content={block.content as ContactContent} onChange={changeContent} />}
        <div className="border-t pt-4">
          <label className="field-label" htmlFor="block-align">Text alignment</label>
          <select id="block-align" className="field" value={block.settings.align} onChange={(event) => onChange({ ...block, settings: { ...block.settings, align: event.target.value as "left" | "center" } })}>
            <option value="left">Left</option><option value="center">Center</option>
          </select>
        </div>
        <div>
          <label className="field-label" htmlFor="block-tone">Surface tone</label>
          <select id="block-tone" className="field" value={block.settings.tone} onChange={(event) => onChange({ ...block, settings: { ...block.settings, tone: event.target.value as PortfolioBlock["settings"]["tone"] } })}>
            <option value="plain">Plain</option><option value="accent">Amethyst accent</option><option value="contrast">High contrast</option>
          </select>
        </div>
      </div>
    </aside>
  );
}

function HeroFields({ content, onChange }: { content: HeroContent; onChange: (content: HeroContent) => void }) {
  return (
    <>
      <Field id="hero-eyebrow" label="Eyebrow" value={content.eyebrow} maxLength={80} onChange={(eyebrow) => onChange({ ...content, eyebrow })} />
      <Field id="hero-heading" label="Heading" value={content.heading} maxLength={140} multiline onChange={(heading) => onChange({ ...content, heading })} />
      <Field id="hero-body" label="Introduction" value={content.body} maxLength={400} multiline onChange={(body) => onChange({ ...content, body })} />
      <Field id="hero-cta-label" label="Button label" value={content.ctaLabel} maxLength={40} onChange={(ctaLabel) => onChange({ ...content, ctaLabel })} />
      <Field id="hero-cta-url" label="Button link" value={content.ctaUrl} maxLength={300} hint="Use #projects, /page, or an https:// URL." onChange={(ctaUrl) => onChange({ ...content, ctaUrl })} />
    </>
  );
}

function TextFields({ content, onChange }: { content: TextContent; onChange: (content: TextContent) => void }) {
  return (
    <>
      <Field id="text-heading" label="Heading" value={content.heading} maxLength={120} onChange={(heading) => onChange({ ...content, heading })} />
      <Field id="text-body" label="Body" value={content.body} maxLength={1200} multiline rows={7} onChange={(body) => onChange({ ...content, body })} />
    </>
  );
}

function ContactFields({ content, onChange }: { content: ContactContent; onChange: (content: ContactContent) => void }) {
  return (
    <>
      <Field id="contact-heading" label="Heading" value={content.heading} maxLength={120} multiline onChange={(heading) => onChange({ ...content, heading })} />
      <Field id="contact-body" label="Invitation" value={content.body} maxLength={400} multiline onChange={(body) => onChange({ ...content, body })} />
      <Field id="contact-email" type="email" label="Email address" value={content.email} maxLength={254} onChange={(email) => onChange({ ...content, email })} />
    </>
  );
}

function ProjectFields({ content, onChange }: { content: ProjectsContent; onChange: (content: ProjectsContent) => void }) {
  function changeItem(index: number, patch: Partial<ProjectsContent["items"][number]>) {
    onChange({ ...content, items: content.items.map((item, itemIndex) => itemIndex === index ? { ...item, ...patch } : item) });
  }
  return (
    <>
      <Field id="projects-heading" label="Section heading" value={content.heading} maxLength={120} onChange={(heading) => onChange({ ...content, heading })} />
      <fieldset className="space-y-4">
        <legend className="field-label">Projects</legend>
        {content.items.map((item, index) => (
          <div key={index} className="rounded-2xl border bg-elevated p-3.5">
            <div className="flex items-center justify-between"><p className="text-xs font-bold text-muted">Project {index + 1}</p>
              <Button variant="ghost" className="size-10 px-0 text-red-700 dark:text-red-300" disabled={content.items.length === 1} onClick={() => onChange({ ...content, items: content.items.filter((_, itemIndex) => itemIndex !== index) })} aria-label={`Remove project ${index + 1}`}><Trash2 size={15} aria-hidden="true" /></Button>
            </div>
            <Field id={`project-${index}-title`} label="Title" value={item.title} maxLength={100} onChange={(title) => changeItem(index, { title })} />
            <div className="mt-3"><Field id={`project-${index}-description`} label="Description" value={item.description} maxLength={300} multiline onChange={(description) => changeItem(index, { description })} /></div>
          </div>
        ))}
        <Button variant="secondary" className="w-full" disabled={content.items.length >= 12} onClick={() => onChange({ ...content, items: [...content.items, { title: "New project", description: "Describe the problem, your contribution and the result." }] })}><Plus size={16} aria-hidden="true" /> Add project</Button>
      </fieldset>
    </>
  );
}

function Field({ id, label, value, onChange, multiline = false, rows = 3, maxLength, hint, type = "text" }: { id: string; label: string; value: string; onChange: (value: string) => void; multiline?: boolean; rows?: number; maxLength: number; hint?: string; type?: string }) {
  const describedBy = hint ? `${id}-hint` : undefined;
  return (
    <div>
      <div className="flex items-baseline justify-between gap-2"><label className="field-label" htmlFor={id}>{label}</label><span className="text-[10px] text-muted">{value.length}/{maxLength}</span></div>
      {multiline ? (
        <textarea id={id} className="field resize-y" rows={rows} maxLength={maxLength} value={value} aria-describedby={describedBy} onChange={(event) => onChange(event.target.value)} />
      ) : (
        <input id={id} type={type} className="field" maxLength={maxLength} value={value} aria-describedby={describedBy} onChange={(event) => onChange(event.target.value)} />
      )}
      {hint && <p id={describedBy} className="mt-1 text-xs leading-4 text-muted">{hint}</p>}
    </div>
  );
}
