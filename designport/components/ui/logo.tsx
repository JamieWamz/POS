import Link from "next/link";

import { cn } from "@/lib/utils";

export function Logo({ className }: { className?: string }) {
  return (
    <Link
      href="/"
      className={cn("inline-flex min-h-11 items-center gap-2 rounded-lg font-bold", className)}
      aria-label="DesignPort home"
    >
      <span
        aria-hidden="true"
        className="grid size-8 place-items-center rounded-xl bg-ink font-display text-lg italic text-canvas"
      >
        D
      </span>
      <span>DesignPort</span>
    </Link>
  );
}
