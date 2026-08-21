"use client";

import { AlertTriangle } from "lucide-react";
import { useEffect } from "react";

import { Button } from "@/components/ui/button";

export default function ErrorPage({ error, reset }: { error: Error & { digest?: string }; reset: () => void }) {
  useEffect(() => { console.error(error); }, [error]);
  return (
    <main id="main-content" tabIndex={-1} className="grid min-h-[70vh] place-items-center px-4 text-center">
      <div className="max-w-md">
        <AlertTriangle className="mx-auto text-acid-500" size={36} aria-hidden="true" />
        <h1 className="mt-5 font-display text-3xl font-semibold">That page lost its footing.</h1>
        <p className="mt-3 text-muted">Your work is safe. Try loading this view again.</p>
        <Button onClick={reset} className="mt-7">Try again</Button>
      </div>
    </main>
  );
}
