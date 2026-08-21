import Link from "next/link";

import { cn } from "@/lib/utils";

export function Logo({ className }: { className?: string }) {
  return (
    <Link
      href="/"
      className={cn("group inline-flex min-h-11 items-center gap-2.5 rounded-lg font-extrabold tracking-[-0.03em]", className)}
      aria-label="DesignPort home"
    >
      <span
        aria-hidden="true"
        className="grid size-9 place-items-center rounded-full bg-ink text-[0.7rem] font-black uppercase tracking-[-0.08em] text-canvas transition-transform duration-240 group-hover:-rotate-6"
      >
        DP
      </span>
      <span className="text-[0.9375rem]">DesignPort<span className="text-signal-500">.</span></span>
    </Link>
  );
}
