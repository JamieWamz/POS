"use client";

import { LoaderCircle, Plus } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Button } from "@/components/ui/button";

export function CreatePortfolioButton({ compact = false }: { compact?: boolean }) {
  const router = useRouter();
  const [pending, setPending] = useState(false);
  const [error, setError] = useState("");

  async function createPortfolio() {
    try {
      setPending(true);
      setError("");
      const response = await fetch("/api/portfolios", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title: "Untitled portfolio" })
      });
      const data = (await response.json()) as { portfolio?: { id: string }; error?: string };
      if (!response.ok || !data.portfolio) throw new Error(data.error ?? "Portfolio creation failed.");
      router.push(`/builder/${data.portfolio.id}`);
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "Portfolio creation failed.");
      setPending(false);
    }
  }

  return (
    <div>
      <Button onClick={createPortfolio} disabled={pending} aria-describedby={error ? "create-error" : undefined}>
        {pending ? <LoaderCircle className="animate-spin motion-reduce:animate-none" size={17} aria-hidden="true" /> : <Plus size={17} aria-hidden="true" />}
        {compact ? "New" : pending ? "Preparing your canvas…" : "New portfolio"}
      </Button>
      {error && <p id="create-error" role="alert" className="mt-2 text-sm text-red-700 dark:text-red-300">{error}</p>}
    </div>
  );
}
