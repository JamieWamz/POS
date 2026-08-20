import Link from "next/link";
import { buttonVariants } from "@/components/ui/button-variants";

export default function NotFound() {
  return (
    <main id="main-content" tabIndex={-1} className="grid min-h-[70vh] place-items-center px-4 text-center">
      <div>
        <p className="eyebrow">404</p>
        <h1 className="mt-3 font-display text-4xl font-semibold">This page is not in the collection.</h1>
        <p className="mt-4 text-muted">The link may have changed, or the portfolio is not published.</p>
        <Link href="/" className={`${buttonVariants("primary")} mt-7`}>Return home</Link>
      </div>
    </main>
  );
}
