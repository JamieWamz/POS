"use client";

export default function GlobalError({ reset }: { error: Error & { digest?: string }; reset: () => void }) {
  return (
    <html lang="en">
      <body style={{ fontFamily: "system-ui", margin: 0, background: "#f7f4ee", color: "#1b1815" }}>
        <main style={{ minHeight: "100vh", display: "grid", placeItems: "center", padding: 24, textAlign: "center" }}>
          <div>
            <h1>DesignPort needs a fresh start.</h1>
            <p>We hit an unexpected application error. Your saved portfolio has not been removed.</p>
            <button onClick={reset} style={{ minHeight: 44, padding: "10px 18px", borderRadius: 12, background: "#1b1815", color: "#fff", border: 0, cursor: "pointer" }}>Reload application</button>
          </div>
        </main>
      </body>
    </html>
  );
}
