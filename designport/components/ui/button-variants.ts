import { cn } from "@/lib/utils";

export type ButtonVariant = "primary" | "secondary" | "ghost" | "danger";

export function buttonVariants(variant: ButtonVariant = "primary") {
  return cn(
    "inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition-colors duration-160 disabled:cursor-not-allowed disabled:opacity-50",
    variant === "primary" &&
      "bg-ink text-canvas shadow-sm hover:bg-amethyst-700 hover:text-white dark:hover:bg-amethyst-400 dark:hover:text-ink",
    variant === "secondary" &&
      "border border-line bg-surface text-ink hover:border-champagne-400 hover:bg-elevated",
    variant === "ghost" && "text-muted hover:bg-elevated hover:text-ink",
    variant === "danger" && "bg-red-700 text-white hover:bg-red-800"
  );
}
