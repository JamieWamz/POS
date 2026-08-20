import { Skeleton } from "@/components/ui/skeleton";

export default function Loading() {
  return (
    <main id="main-content" tabIndex={-1} className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8" aria-busy="true" aria-label="Loading page">
      <span className="sr-only">Loading…</span>
      <Skeleton className="h-4 w-28" />
      <Skeleton className="mt-4 h-12 max-w-xl" />
      <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {[0, 1, 2].map((item) => <Skeleton key={item} className="h-56" />)}
      </div>
    </main>
  );
}
