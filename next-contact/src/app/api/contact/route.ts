export const runtime = "nodejs";
import { NextRequest, NextResponse } from "next/server";

export async function POST(req: NextRequest) {
  const data = await req.json();

  // ⚠️ Ako tvoj endpoint traži nonce, ili ga ukloni iz permission_callback,
  // ili napravi javnu varijantu rute (npr. /wi/v1/contact-public sa __return_true)
  const wpRes = await fetch(process.env.WP_CONTACT_URL!, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });

  const text = await wpRes.text();
  return new NextResponse(text, { status: wpRes.status });
}
