"use client";

import { motion, useReducedMotion } from "framer-motion";
import { Accessibility, Blocks, Sparkles } from "lucide-react";

const features = [
  {
    icon: Blocks,
    title: "Compose, don’t configure",
    text: "Drag focused sections into place and shape the story through direct manipulation."
  },
  {
    icon: Sparkles,
    title: "Editorial by default",
    text: "Every theme starts with considered typography, rhythm and contrast—not a blank canvas."
  },
  {
    icon: Accessibility,
    title: "Inclusive from the start",
    text: "Keyboard controls, semantic output and visible focus are built into every portfolio."
  }
];

export function FeatureGrid() {
  const reduceMotion = useReducedMotion();

  return (
    <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" aria-labelledby="features-title">
      <div className="max-w-2xl">
        <p className="eyebrow">A better starting point</p>
        <h2 id="features-title" className="mt-3 font-display text-3xl font-semibold sm:text-4xl">
          The restraint of a studio. The speed of a tool.
        </h2>
      </div>
      <div className="mt-10 grid gap-4 md:grid-cols-3">
        {features.map((feature, index) => (
          <motion.article
            key={feature.title}
            initial={reduceMotion ? false : { opacity: 0, y: 18 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-80px" }}
            transition={{ duration: reduceMotion ? 0 : 0.4, delay: index * 0.08 }}
            whileHover={reduceMotion ? undefined : { y: -4 }}
            className="rounded-3xl border bg-surface p-6 shadow-luxury sm:p-8"
          >
            <span className="grid size-11 place-items-center rounded-2xl bg-amethyst-500/10 text-amethyst-700 dark:text-amethyst-400">
              <feature.icon aria-hidden="true" size={21} />
            </span>
            <h3 className="mt-8 font-display text-xl font-semibold">{feature.title}</h3>
            <p className="mt-3 text-sm leading-6 text-muted">{feature.text}</p>
          </motion.article>
        ))}
      </div>
    </section>
  );
}
