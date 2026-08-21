import { cn } from "@/lib/utils";

export type ButtonVariant = "primary" | "secondary" | "ghost" | "danger";

export function buttonVariants(variant: ButtonVariant = "primary") {
  return cn(
    "inline-flex min-h-11 items-center justify-center gap-2 rounded-full px-5 py-2 text-sm font-extrabold tracking-[-0.01em] transition duration-160 disabled:cursor-not-allowed disabled:opacity-50",
    variant === "primary" &&
      "bg-signal-600 text-white shadow-[0_8px_24px_-12px_rgb(59_85_230/0.9)] hover:-translate-y-0.5 hover:bg-signal-700 dark:bg-signal-500 dark:hover:bg-signal-400 dark:hover:text-neutral-950",
    variant === "secondary" &&
      "border border-line bg-surface text-ink shadow-sm hover:-translate-y-0.5 hover:border-ink/40 hover:bg-elevated",
    variant === "ghost" && "px-4 text-muted hover:bg-elevated hover:text-ink",
    variant === "danger" && "bg-red-700 text-white hover:-translate-y-0.5 hover:bg-red-800"
  );
}
