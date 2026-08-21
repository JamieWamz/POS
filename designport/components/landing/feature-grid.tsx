"use client";

import { motion, useReducedMotion } from "framer-motion";
import { Accessibility, ArrowDown, Blocks, Command, Globe2, WandSparkles } from "lucide-react";

const features = [
  {
    icon: Blocks,
    index: "01",
    title: "Compose with intent",
    text: "A deliberately small block library keeps the focus on hierarchy, pacing, and what your work actually says.",
    className: "md:col-span-2 lg:col-span-2 bg-signal-600 text-white",
    iconClass: "bg-white/[.15] text-white"
  },
  {
    icon: WandSparkles,
    index: "02",
    title: "Art direction included",
    text: "Typography, contrast, spacing, and responsive rhythm arrive considered from the first block.",
    className: "bg-acid-400 text-neutral-950",
    iconClass: "bg-neutral-950 text-acid-400"
  },
  {
    icon: Command,
    index: "03",
    title: "Move at thought speed",
    text: "Drag, duplicate, undo, and refine without leaving the canvas or waiting on a handoff.",
    className: "bg-surface text-ink",
    iconClass: "bg-ink text-canvas"
  },
  {
    icon: Accessibility,
    index: "04",
    title: "Inclusive by construction",
    text: "Semantic sections, visible focus, keyboard reordering, reduced motion, and contrast-aware themes are built in.",
    className: "md:col-span-2 bg-ink text-canvas",
    iconClass: "bg-acid-400 text-neutral-950"
  },
  {
    icon: Globe2,
    index: "05",
    title: "One click to live",
    text: "Publish a fast, canonical page and keep the editing interface completely out of your audience’s way.",
    className: "bg-[#ff7657] text-neutral-950",
    iconClass: "bg-neutral-950 text-white"
  }
];

export function FeatureGrid() {
  const reduceMotion = useReducedMotion();

  return (
    <section id="features" className="scroll-mt-24 border-b bg-canvas px-4 py-24 sm:px-6 lg:px-8 lg:py-32" aria-labelledby="features-title">
      <div className="mx-auto max-w-[1536px]">
        <div className="grid gap-8 lg:grid-cols-[.65fr_1fr] lg:items-end">
          <div>
            <p className="eyebrow">The system / 02</p>
            <h2 id="features-title" className="mt-5 max-w-2xl font-display text-4xl font-semibold leading-[.95] tracking-[-0.04em] sm:text-5xl lg:text-6xl">
              Enough structure.<br /><span className="italic text-signal-600 dark:text-signal-400">Plenty of voice.</span>
            </h2>
          </div>
          <div className="flex items-end justify-between gap-6 border-t pt-5 lg:ml-auto lg:max-w-xl">
            <p className="max-w-md text-sm leading-7 text-muted sm:text-base">DesignPort removes configuration without removing creative control. Every decision serves a stronger final story.</p>
            <ArrowDown className="hidden shrink-0 text-signal-500 sm:block" size={28} aria-hidden="true" />
          </div>
        </div>

        <div className="mt-14 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
          {features.map((feature, index) => (
            <motion.article
              key={feature.title}
              initial={reduceMotion ? false : { opacity: 0, y: 24 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, margin: "-80px" }}
              transition={{ duration: reduceMotion ? 0 : 0.45, delay: index * 0.06 }}
              whileHover={reduceMotion ? undefined : { y: -5 }}
              className={`group flex min-h-[21rem] flex-col rounded-[1.75rem] border border-black/10 p-6 shadow-luxury sm:p-8 ${feature.className}`}
            >
              <div className="flex items-start justify-between">
                <span className={`grid size-12 place-items-center rounded-full ${feature.iconClass}`}><feature.icon size={20} aria-hidden="true" /></span>
                <span className="font-mono text-[0.6875rem] font-bold tracking-[.18em] opacity-[.55]">{feature.index}</span>
              </div>
              <div className="mt-auto">
                <h3 className="font-display text-3xl font-semibold leading-none tracking-[-0.03em]">{feature.title}</h3>
                <p className="mt-4 max-w-lg text-sm leading-6 opacity-[.72]">{feature.text}</p>
              </div>
            </motion.article>
          ))}
        </div>
      </div>
    </section>
  );
}
