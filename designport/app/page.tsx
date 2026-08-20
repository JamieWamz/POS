import { ArrowRight } from "lucide-react";
import Link from "next/link";

import { FeatureGrid } from "@/components/landing/feature-grid";
import { Hero } from "@/components/landing/hero";
import { buttonVariants } from "@/components/ui/button-variants";

export default function HomePage() {
  return (
    <main id="main-content" tabIndex={-1}>
      <Hero />
      <FeatureGrid />
      <section id="how-it-works" className="border-y bg-ink text-canvas">
        <div className="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[.7fr_1fr] lg:px-8">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.18em] text-champagne-200">Three quiet steps</p>
            <h2 className="mt-3 font-display text-3xl font-semibold sm:text-4xl">From blank page to considered presence.</h2>
          </div>
          <ol className="grid gap-6 sm:grid-cols-3">
            {[
              ["01", "Compose", "Choose only the sections your story needs."],
              ["02", "Refine", "Edit content and tune tone in one focused inspector."],
              ["03", "Publish", "Share a fast, responsive page on your own URL."]
            ].map(([number, title, text]) => (
              <li key={number} className="border-t border-white/20 pt-4">
                <span className="text-xs text-champagne-200">{number}</span>
                <h3 className="mt-5 font-display text-xl font-semibold">{title}</h3>
                <p className="mt-2 text-sm leading-6 text-white/70">{text}</p>
              </li>
            ))}
          </ol>
        </div>
      </section>
      <section className="mx-auto max-w-4xl px-4 py-24 text-center sm:px-6">
        <p className="eyebrow">Make the first move</p>
        <h2 className="mt-3 font-display text-4xl font-semibold">Your next opportunity may already be looking.</h2>
        <Link href="/auth/signin" className={`${buttonVariants("primary")} mt-8`}>
          Build for free <ArrowRight size={17} aria-hidden="true" />
        </Link>
      </section>
      <footer className="border-t px-4 py-8 text-center text-sm text-muted">
        © {new Date().getFullYear()} DesignPort. Built for expressive work.
      </footer>
    </main>
  );
}
