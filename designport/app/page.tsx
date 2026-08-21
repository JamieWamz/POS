import { ArrowRight, Asterisk, Check } from "lucide-react";
import Link from "next/link";

import { FeatureGrid } from "@/components/landing/feature-grid";
import { Hero } from "@/components/landing/hero";
import { Logo } from "@/components/ui/logo";
import { buttonVariants } from "@/components/ui/button-variants";

const steps = [
  ["01", "Compose", "Start with the story. Add only the sections that earn their place."],
  ["02", "Direct", "Shape copy, emphasis, tone, and order in the same visual workspace."],
  ["03", "Release", "Publish a responsive page with accessible structure and clean metadata."]
];

export default function HomePage() {
  return (
    <main id="main-content" tabIndex={-1}>
      <Hero />
      <FeatureGrid />

      <section id="process" className="scroll-mt-24 bg-surface" aria-labelledby="process-title">
        <div className="mx-auto grid max-w-[1536px] lg:grid-cols-[.7fr_1.3fr]">
          <div className="border-b p-6 sm:p-10 lg:sticky lg:top-[4.5rem] lg:h-[calc(100vh-4.5rem)] lg:border-b-0 lg:border-r lg:p-14">
            <p className="eyebrow">The process / 03</p>
            <h2 id="process-title" className="mt-5 max-w-lg font-display text-4xl font-semibold leading-[.95] tracking-[-0.04em] sm:text-5xl lg:text-6xl">From rough idea to <span className="italic text-signal-600 dark:text-signal-400">live presence.</span></h2>
            <p className="mt-6 max-w-md text-sm leading-7 text-muted">The interface follows the way creative work actually develops: make a move, see the result, refine, repeat.</p>
            <div className="mt-10 hidden size-28 place-items-center rounded-full border border-line bg-canvas lg:grid" aria-hidden="true"><Asterisk className="text-signal-500" size={40} /></div>
          </div>
          <ol className="divide-y divide-line">
            {steps.map(([number, title, text]) => (
              <li key={number} className="group grid min-h-[17rem] gap-8 p-6 transition-colors hover:bg-canvas sm:grid-cols-[5rem_1fr] sm:p-10 lg:p-14">
                <span className="font-mono text-xs font-bold tracking-[.18em] text-signal-600 dark:text-signal-400">{number}</span>
                <div className="self-end">
                  <h3 className="font-display text-4xl font-semibold tracking-[-0.03em] sm:text-5xl">{title}</h3>
                  <p className="mt-4 max-w-xl text-sm leading-7 text-muted sm:text-base">{text}</p>
                  <span className="mt-7 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[.12em]"><Check size={15} className="text-signal-500" aria-hidden="true" /> Built for iteration</span>
                </div>
              </li>
            ))}
          </ol>
        </div>
      </section>

      <section className="overflow-hidden border-y bg-signal-600 text-white">
        <div className="mx-auto grid max-w-[1536px] gap-10 px-4 py-20 sm:px-6 sm:py-28 lg:grid-cols-[1fr_auto] lg:items-end lg:px-8">
          <div>
            <p className="text-xs font-extrabold uppercase tracking-[.18em] text-acid-200">Your work is already ready / 04</p>
            <h2 className="mt-5 max-w-5xl font-display text-5xl font-semibold leading-[.88] tracking-[-0.05em] sm:text-6xl lg:text-7xl">Give it a place with <span className="italic text-acid-400">presence.</span></h2>
          </div>
          <Link href="/auth/signin" className={`${buttonVariants("secondary")} shrink-0 border-white/20 bg-white text-neutral-950 hover:bg-acid-400`}>
            Build for free <ArrowRight size={17} aria-hidden="true" />
          </Link>
        </div>
      </section>

      <footer className="bg-ink px-4 py-8 text-white sm:px-6 lg:px-8">
        <div className="mx-auto flex max-w-[1536px] flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
          <Logo className="text-white [&>span:first-child]:bg-white [&>span:first-child]:text-neutral-950" />
          <p className="text-xs font-semibold text-white/50">© {new Date().getFullYear()} DesignPort. Built for work with a point of view.</p>
        </div>
      </footer>
    </main>
  );
}
