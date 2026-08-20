import { NextResponse } from "next/server";

export function apiError(message: string, status: number) {
  return NextResponse.json({ error: message }, { status });
}

export function unknownApiError(error: unknown) {
  console.error(error);
  return apiError("An unexpected server error occurred.", 500);
}
