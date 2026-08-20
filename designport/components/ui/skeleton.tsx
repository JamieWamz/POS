import { cn } from "@/lib/utils";

export function Skeleton({ className }: { className?: string }) {
  return (
    <div
      className={cn("animate-pulse rounded-xl bg-line/60 motion-reduce:animate-none", className)}
      aria-hidden="true"
    />
  );
}
