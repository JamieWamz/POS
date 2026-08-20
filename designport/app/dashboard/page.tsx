import type { Metadata } from "next";
import { redirect } from "next/navigation";

import { CreatePortfolioButton } from "@/components/dashboard/create-portfolio-button";
import { PortfolioCard } from "@/components/dashboard/portfolio-card";
import { auth } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

export const metadata: Metadata = { title: "Your portfolios" };

export default async function DashboardPage() {
  const session = await auth();
  if (!session?.user.id) redirect("/auth/signin");

  const portfolios = await prisma.portfolio.findMany({
    where: { userId: session.user.id },
    orderBy: { updatedAt: "desc" },
    select: { id: true, title: true, slug: true, description: true, publishedAt: true, updatedAt: true }
  });

  return (
    <main id="main-content" tabIndex={-1} className="mx-auto min-h-[calc(100vh-4rem)] max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
      <div className="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
        <div>
          <p className="eyebrow">Your studio</p>
          <h1 className="mt-3 font-display text-4xl font-semibold">Welcome back{session.user.name ? `, ${session.user.name.split(" ")[0]}` : ""}.</h1>
          <p className="mt-3 text-muted">Shape, publish and revisit every version of your work.</p>
        </div>
        <CreatePortfolioButton />
      </div>

      {portfolios.length > 0 ? (
        <section className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" aria-label="Your portfolios">
          {portfolios.map((portfolio) => <PortfolioCard key={portfolio.id} portfolio={portfolio} />)}
        </section>
      ) : (
        <section className="mt-12 rounded-3xl border border-dashed bg-surface px-6 py-20 text-center" aria-labelledby="empty-title">
          <span aria-hidden="true" className="mx-auto grid size-14 place-items-center rounded-2xl bg-amethyst-500/10 font-display text-2xl italic text-amethyst-700 dark:text-amethyst-400">D</span>
          <h2 id="empty-title" className="mt-6 font-display text-2xl font-semibold">Begin with one strong page.</h2>
          <p className="mx-auto mt-3 max-w-md text-sm leading-6 text-muted">Your first portfolio starts with three sensible sections. Rearrange or replace every one.</p>
          <div className="mt-7 flex justify-center"><CreatePortfolioButton compact /></div>
        </section>
      )}
    </main>
  );
}
