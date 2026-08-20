"use client";

import { motion, useReducedMotion } from "framer-motion";
import { ArrowRight, GripVertical } from "lucide-react";
import Link from "next/link";

import { buttonVariants } from "@/components/ui/button-variants";

export function Hero() {
  const reduceMotion = useReducedMotion();

  return (
    <section className="relative overflow-hidden border-b">
      <div aria-hidden="true" className="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(124,92,229,0.14),transparent_34%),radial-gradient(circle_at_20%_80%,rgba(201,170,103,0.16),transparent_30%)]" />
      <div className="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 sm:py-28 lg:grid-cols-[1fr_.9fr] lg:px-8 lg:py-36">
        <motion.div
          initial={reduceMotion ? false : { opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: reduceMotion ? 0 : 0.4 }}
        >
          <p className="eyebrow">Your work deserves presence</p>
          <h1 className="mt-4 max-w-3xl font-display text-4xl font-semibold tracking-[-0.025em] sm:text-5xl lg:text-[4.6rem] lg:leading-[0.94]">
            Build a portfolio that feels unmistakably yours.
          </h1>
          <p className="mt-6 max-w-xl text-base leading-7 text-muted sm:text-lg">
            DesignPort turns focused content blocks into a polished, accessible landing page—without flattening your point of view.
          </p>
          <div className="mt-8 flex flex-col gap-3 sm:flex-row">
            <Link href="/auth/signin" className={buttonVariants("primary")}>
              Create your portfolio <ArrowRight size={17} aria-hidden="true" />
            </Link>
            <a href="#how-it-works" className={buttonVariants("secondary")}>See how it works</a>
          </div>
        </motion.div>

        <motion.div
          initial={reduceMotion ? false : { opacity: 0, scale: 0.96, rotate: 1 }}
          animate={{ opacity: 1, scale: 1, rotate: 0 }}
          transition={{ duration: reduceMotion ? 0 : 0.5, delay: 0.12 }}
          className="glass-panel relative mx-auto w-full max-w-lg rounded-3xl p-3"
          aria-label="Preview of the DesignPort page builder"
        >
          <div className="flex items-center justify-between border-b px-3 py-2 text-xs text-muted">
            <span>Live canvas</span><span>Saved</span>
          </div>
          <div className="space-y-3 p-3">
            <PreviewCard label="Hero" className="min-h-44 bg-ink text-canvas">
              <p className="max-w-xs font-display text-2xl font-semibold">Design for people, built for change.</p>
              <div className="mt-4 h-2 w-28 rounded-full bg-champagne-400" />
            </PreviewCard>
            <PreviewCard label="Projects" className="min-h-28 bg-elevated">
              <div className="grid grid-cols-2 gap-2"><div className="h-16 rounded-xl bg-line/70" /><div className="h-16 rounded-xl bg-line/70" /></div>
            </PreviewCard>
          </div>
        </motion.div>
      </div>
    </section>
  );
}

function PreviewCard({ label, className, children }: { label: string; className: string; children: React.ReactNode }) {
  return (
    <div className={`relative rounded-2xl p-6 ${className}`}>
      <span className="absolute right-3 top-3 inline-flex items-center gap-1 rounded-lg bg-canvas/90 px-2 py-1 text-[10px] font-bold text-ink shadow-sm">
        <GripVertical size={11} aria-hidden="true" />{label}
      </span>
      {children}
    </div>
  );
}
