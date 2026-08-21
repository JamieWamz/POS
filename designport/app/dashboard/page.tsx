import type { Metadata } from "next";
import { Clock3, Eye, Layers3 } from "lucide-react";
import { redirect } from "next/navigation";

import { CreatePortfolioButton } from "@/components/dashboard/create-portfolio-button";
import { PortfolioCard } from "@/components/dashboard/portfolio-card";
import { auth } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

export const metadata: Metadata = { title: "Your studio" };

export default async function DashboardPage() {
  const session = await auth();
  if (!session?.user.id) redirect("/auth/signin");

  const portfolios = await prisma.portfolio.findMany({
    where: { userId: session.user.id },
    orderBy: { updatedAt: "desc" },
    select: { id: true, title: true, slug: true, description: true, theme: true, publishedAt: true, updatedAt: true }
  });
  const publishedCount = portfolios.filter((portfolio) => portfolio.publishedAt).length;

  return (
    <main id="main-content" tabIndex={-1} className="min-h-[calc(100vh-4.5rem)]">
      <section className="border-b bg-ink text-white">
        <div className="mx-auto grid max-w-[1536px] gap-12 px-4 py-14 sm:px-6 sm:py-16 lg:grid-cols-[1fr_auto] lg:items-end lg:px-8">
          <div>
            <p className="text-xs font-extrabold uppercase tracking-[.18em] text-acid-400">Private workspace / Studio 01</p>
            <h1 className="mt-5 max-w-4xl font-display text-5xl font-semibold leading-[.9] tracking-[-0.045em] sm:text-6xl">Good to see you{session.user.name ? `, ${session.user.name.split(" ")[0]}` : ""}.</h1>
            <p className="mt-5 max-w-2xl text-sm leading-7 text-white/60 sm:text-base">Pick up a draft, shape a new story, or revisit what the world already sees.</p>
          </div>
          <CreatePortfolioButton />
        </div>
        <div className="mx-auto grid max-w-[1536px] grid-cols-3 divide-x divide-white/[.15] border-t border-white/[.15] px-4 sm:px-6 lg:px-8">
          <Stat icon={Layers3} value={portfolios.length} label="Portfolios" />
          <Stat icon={Eye} value={publishedCount} label="Published" />
          <Stat icon={Clock3} value={portfolios.length - publishedCount} label="In progress" />
        </div>
      </section>

      <div className="mx-auto max-w-[1536px] px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div className="flex items-center justify-between border-b pb-5">
          <div><p className="eyebrow">Selected work</p><h2 className="mt-3 font-display text-3xl font-semibold">Your portfolios</h2></div>
          <span className="index-label">{String(portfolios.length).padStart(2, "0")} total</span>
        </div>
        {portfolios.length > 0 ? (
          <section className="mt-7 grid gap-5 sm:grid-cols-2 xl:grid-cols-3" aria-label="Your portfolios">
            {portfolios.map((portfolio, index) => <PortfolioCard key={portfolio.id} portfolio={portfolio} index={index} />)}
          </section>
        ) : (
          <section className="editorial-grid mt-8 overflow-hidden rounded-[2rem] border bg-surface" aria-labelledby="empty-title">
            <div className="grid min-h-[28rem] place-items-center p-8 text-center">
              <div>
                <span aria-hidden="true" className="mx-auto grid size-16 place-items-center rounded-full bg-acid-400 font-display text-3xl italic text-neutral-950">D</span>
                <h2 id="empty-title" className="mt-7 font-display text-3xl font-semibold">The first page is yours.</h2>
                <p className="mx-auto mt-3 max-w-md text-sm leading-7 text-muted">Begin with a considered structure, then make every section sound and feel like you.</p>
                <div className="mt-8 flex justify-center"><CreatePortfolioButton compact /></div>
              </div>
            </div>
          </section>
        )}
      </div>
    </main>
  );
}

function Stat({ icon: Icon, value, label }: { icon: typeof Layers3; value: number; label: string }) {
  return <div className="flex items-center gap-3 px-3 py-5 first:pl-0 sm:px-6"><Icon size={17} className="hidden text-acid-400 sm:block" aria-hidden="true" /><div><p className="font-display text-2xl font-semibold">{String(value).padStart(2, "0")}</p><p className="text-[0.625rem] font-bold uppercase tracking-[.14em] text-white/[.45]">{label}</p></div></div>;
}
