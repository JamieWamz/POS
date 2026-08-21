"use client";

import { motion, useReducedMotion } from "framer-motion";
import { ArrowRight, Check, GripVertical, Plus, Sparkles } from "lucide-react";
import Link from "next/link";

import { buttonVariants } from "@/components/ui/button-variants";

export function Hero() {
  const reduceMotion = useReducedMotion();

  return (
    <section className="relative overflow-hidden bg-ink text-canvas">
      <div aria-hidden="true" className="absolute inset-0 opacity-30 [background-image:linear-gradient(rgba(255,255,255,.12)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.12)_1px,transparent_1px)] [background-size:80px_80px]" />
      <div aria-hidden="true" className="absolute -right-24 top-12 size-[28rem] rounded-full bg-signal-500/30 blur-[100px]" />

      <div className="relative mx-auto grid min-h-[calc(100svh-4.5rem)] max-w-[1536px] items-center gap-14 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[minmax(0,1.04fr)_minmax(28rem,.96fr)] lg:px-8 xl:gap-20">
        <motion.div
          initial={reduceMotion ? false : { opacity: 0, y: 24 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: reduceMotion ? 0 : 0.5, ease: [0.22, 1, 0.36, 1] }}
          className="relative z-10"
        >
          <div className="flex items-center gap-3">
            <span className="signal-chip">Portfolio builder / 01</span>
            <span className="hidden text-xs font-bold text-white/50 sm:inline">No code. No template energy.</span>
          </div>
          <h1 className="mt-7 max-w-4xl font-display text-[3.25rem] font-semibold leading-[0.88] tracking-[-0.055em] sm:text-[4.75rem] lg:text-[5.4rem] xl:text-[6.4rem]">
            Your work,<br />with a <span className="italic text-acid-400">point of view.</span>
          </h1>
          <p className="mt-7 max-w-xl text-base leading-7 text-white/[.68] sm:text-lg sm:leading-8">
            Build an art-directed portfolio through direct manipulation. Compose the story, tune every detail, and publish a page that feels authored—not assembled.
          </p>
          <div className="mt-9 flex flex-col gap-3 sm:flex-row">
            <Link href="/auth/signin" className={buttonVariants("primary")}>
              Start your portfolio <ArrowRight size={17} aria-hidden="true" />
            </Link>
            <a href="#features" className="inline-flex min-h-11 items-center justify-center rounded-full border border-white/25 px-5 text-sm font-extrabold text-white transition hover:border-white/60 hover:bg-white/10">
              Explore the system
            </a>
          </div>
          <div className="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-xs font-semibold text-white/[.55]">
            {['Keyboard accessible', 'Autosaves as you work', 'Publish in one click'].map((item) => (
              <span key={item} className="inline-flex items-center gap-2"><Check size={14} className="text-acid-400" aria-hidden="true" />{item}</span>
            ))}
          </div>
        </motion.div>

        <BuilderPreview reduceMotion={Boolean(reduceMotion)} />
      </div>

      <div className="relative border-t border-white/[.15]">
        <div className="mx-auto grid max-w-[1536px] grid-cols-2 divide-x divide-white/[.15] px-4 sm:px-6 md:grid-cols-4 lg:px-8">
          {[["04", "Focused block types"], ["03", "Art-directed themes"], ["AA", "Accessible by default"], ["∞", "Revisions encouraged"]].map(([value, label]) => (
            <div key={label} className="px-4 py-5 first:pl-0 md:px-6">
              <p className="font-display text-2xl font-semibold text-white">{value}</p>
              <p className="mt-1 text-[0.6875rem] font-bold uppercase tracking-[0.12em] text-white/[.45]">{label}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function BuilderPreview({ reduceMotion }: { reduceMotion: boolean }) {
  return (
    <motion.div
      initial={reduceMotion ? false : { opacity: 0, scale: 0.94, rotate: 1.5 }}
      animate={{ opacity: 1, scale: 1, rotate: 0 }}
      transition={{ duration: reduceMotion ? 0 : 0.65, delay: 0.12, ease: [0.22, 1, 0.36, 1] }}
      className="relative mx-auto w-full max-w-[40rem] lg:mr-0"
      aria-label="Preview of the DesignPort visual editor"
    >
      <div aria-hidden="true" className="absolute -bottom-5 -right-5 size-full rounded-[2rem] bg-acid-400" />
      <div className="relative overflow-hidden rounded-[1.6rem] border border-white/[.15] bg-[#f4f1ea] text-[#151619] shadow-[0_36px_90px_-30px_rgba(0,0,0,.72)]">
        <div className="flex h-12 items-center gap-2 border-b border-black/10 bg-white/75 px-4">
          <span className="size-2.5 rounded-full bg-[#ff625d]" /><span className="size-2.5 rounded-full bg-[#ffc043]" /><span className="size-2.5 rounded-full bg-[#55c878]" />
          <span className="ml-3 font-mono text-[10px] uppercase tracking-[.16em] text-black/[.45]">DesignPort / Live studio</span>
          <span className="ml-auto inline-flex items-center gap-1.5 rounded-full bg-[#d8ff57] px-2.5 py-1 text-[9px] font-black uppercase tracking-wider"><Check size={10} /> Saved</span>
        </div>
        <div className="grid min-h-[29rem] grid-cols-[5.5rem_1fr] sm:grid-cols-[8.5rem_1fr]">
          <div className="border-r border-black/10 bg-white/[.55] p-2.5 sm:p-3">
            <p className="hidden font-mono text-[9px] font-bold uppercase tracking-widest text-black/40 sm:block">Blocks</p>
            <div className="mt-3 space-y-2">
              {["Hero", "Story", "Projects", "Contact"].map((item, index) => (
                <div key={item} className="flex min-h-11 items-center gap-2 rounded-xl border border-black/10 bg-white p-2 shadow-sm">
                  <span className="grid size-7 shrink-0 place-items-center rounded-lg bg-[#151619] text-white"><Plus size={12} /></span>
                  <span className="hidden text-[10px] font-extrabold sm:block">{item}</span>
                  {index === 0 && <GripVertical className="ml-auto hidden text-black/25 sm:block" size={12} />}
                </div>
              ))}
            </div>
          </div>
          <div className="editorial-grid p-3 sm:p-5">
            <div className="overflow-hidden rounded-2xl border border-black/10 bg-[#151619] text-white shadow-xl">
              <div className="relative min-h-[16.5rem] p-6 sm:p-8">
                <div className="absolute right-5 top-5 grid size-11 place-items-center rounded-full bg-[#3b55e6] text-white"><Sparkles size={16} /></div>
                <p className="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#d8ff57]">Independent designer</p>
                <p className="mt-14 max-w-sm font-display text-3xl font-semibold leading-[.94] sm:text-4xl">Ideas made visible. Systems made human.</p>
                <div className="mt-6 h-2 w-20 rounded-full bg-[#d8ff57]" />
              </div>
              <div className="grid grid-cols-2 gap-px bg-white/[.15]">
                <div className="bg-[#ebe4ff] p-4 text-[#151619]"><span className="font-mono text-[8px]">01 / FIELD NOTES</span><div className="mt-7 h-2 w-3/4 rounded bg-black/[.15]" /></div>
                <div className="bg-[#ff7657] p-4 text-[#151619]"><span className="font-mono text-[8px]">02 / KINETIC TYPE</span><div className="mt-7 h-2 w-1/2 rounded bg-black/[.15]" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </motion.div>
  );
}
