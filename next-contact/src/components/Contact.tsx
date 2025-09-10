"use client";
import { useState } from "react";
import { ThumbsUp } from "lucide-react";
import LeafletMap from "@/components/LeafletMap";

const budgetOptions = [
  "< 5.000€",
  "5.000€ - 15.000€",
  "15.000€ - 50.000€",
  "> 50.000€",
];
const timelineOptions = [
  "Sofort",
  "Innerhalb 1 Monat",
  "1-3 Monate",
  "> 3 Monate",
];
const mediumOptions = [
  "Außenwerbung",
  "Beschriftung",
  "Grafikdesign",
  "Webdesign",
];

export default function Contact() {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    company: "",
    phone: "",
    budget: "",
    timeline: "",
    medium: "",
    message: "",
  });
  const [submitting, setSubmitting] = useState(false);
  const [showSuccess, setShowSuccess] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const onChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => setFormData((p) => ({ ...p, [e.target.name]: e.target.value }));

  const onPick = (k: "budget" | "timeline" | "medium", v: string) =>
    setFormData((p) => ({ ...p, [k]: v }));

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    if (!formData.name || !formData.email) {
      setError("Bitte füllen Sie Name und E-Mail aus.");
      return;
    }
    setSubmitting(true);
    try {
      const WP_URL =
        process.env.NEXT_PUBLIC_WP_CONTACT_URL || "/wp-json/wi/v1/contact";
      const res = await fetch(WP_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
        credentials: "include",
      });
      const text = await res.text();
      if (!res.ok)
        throw new Error(`WP ${res.status}: ${text || "Fehler beim Senden"}`);
      setShowSuccess(true);
      setTimeout(() => setShowSuccess(false), 5000);
      setFormData({
        name: "",
        email: "",
        company: "",
        phone: "",
        budget: "",
        timeline: "",
        medium: "",
        message: "",
      });
    } catch (err: unknown) {
      if (err instanceof Error) setError(err.message);
      else setError("Unerwarteter Fehler");
    } finally {
      setSubmitting(false);
    }
  };

  const Select = ({
    options,
    selected,
    onPick,
  }: {
    options: string[];
    selected: string;
    onPick: (v: string) => void;
  }) => (
    <div className="space-y-2">
      {options.map((op) => (
        <button
          key={op}
          type="button"
          onClick={() => onPick(op)}
          className={`w-full p-4 rounded-[40px] text-center transition-all poppins font-bold text-lg ${
            selected === op
              ? "bg-[var(--brand)] text-black border-4 border-black"
              : "bg-black text-white border-2 border-black hover:bg-neutral-800"
          }`}
        >
          {op}
        </button>
      ))}
    </div>
  );

  return (
    <div id="wi-contact-root" className="bg-[var(--brand)] relative">
      {/* HEADER */}
      <header className="py-8">
        <div className="content-container text-center"></div>
      </header>

      <main className="pb-32">
        <div className="relative">
          {/* Naslov */}
          <section className="content-container text-center">
            <h1 className="unbounded-bold text-black mb-8 leading-none break-words text-[clamp(46px,12vw,240px)]">
              KONTAKT
            </h1>
          </section>

          {/* Forma */}
          <section className="content-container mt-16">
            <form onSubmit={onSubmit} className="space-y-12">
              {error && (
                <div className="p-4 border-2 border-black rounded-2xl bg-white text-red-600 poppins">
                  {error}
                </div>
              )}

              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <input
                  type="text"
                  name="name"
                  placeholder="Name *"
                  value={formData.name}
                  onChange={onChange}
                  required
                  className="p-6 rounded-[40px] border-2 border-black bg-white text-black placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-black poppins"
                />
                <input
                  type="email"
                  name="email"
                  placeholder="E-Mail *"
                  value={formData.email}
                  onChange={onChange}
                  required
                  className="p-6 rounded-[40px] border-2 border-black bg-white text-black placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-black poppins"
                />
                <input
                  type="text"
                  name="company"
                  placeholder="Unternehmen"
                  value={formData.company}
                  onChange={onChange}
                  className="p-6 rounded-[40px] border-2 border-black bg-white text-black placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-black poppins"
                />
                <input
                  type="tel"
                  name="phone"
                  placeholder="Telefon"
                  value={formData.phone}
                  onChange={onChange}
                  className="p-6 rounded-[40px] border-2 border-black bg-white text-black placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-black poppins"
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                  <label className="block text-3xl unbounded-bold text-black mb-6 text-center">
                    SERVICES
                  </label>
                  <Select
                    options={mediumOptions}
                    selected={formData.medium}
                    onPick={(v) => onPick("medium", v)}
                  />
                </div>
                <div>
                  <label className="block text-3xl unbounded-bold text-black mb-6 text-center">
                    BUDGET
                  </label>
                  <Select
                    options={budgetOptions}
                    selected={formData.budget}
                    onPick={(v) => onPick("budget", v)}
                  />
                </div>
                <div>
                  <label className="block text-3xl unbounded-bold text-black mb-6 text-center">
                    ZEITRAHMEN
                  </label>
                  <Select
                    options={timelineOptions}
                    selected={formData.timeline}
                    onPick={(v) => onPick("timeline", v)}
                  />
                </div>
              </div>

              <textarea
                name="message"
                placeholder="Nachricht"
                rows={6}
                value={formData.message}
                onChange={onChange}
                className="w-full p-6 rounded-[40px] border-2 border-black bg-white text-black placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-black resize-none poppins"
              />

              <div className="grid gap-3 sm:grid-cols-[1fr_auto] items-center">
                <p className="poppins text-neutral-700 text-[11px] sm:text-[13px] md:text-sm leading-snug break-words hyphens-auto min-w-0">
                  Mit dem Absenden stimmen Sie unseren Datenschutzbestimmungen
                  zu. Ihre Daten werden vertraulich behandelt.
                </p>

                <button
                  type="submit"
                  disabled={submitting}
                  className="w-full sm:w-auto justify-self-stretch sm:justify-self-end bg-[var(--brand)] text-black border-2 border-black rounded-[40px] px-8 sm:px-12 py-4 unbounded-bold hover:bg-black hover:text-white transition-all disabled:opacity-60"
                >
                  {submitting ? "SENDE…" : "ANFRAGE SENDEN"}
                </button>
              </div>
            </form>
          </section>

          {/* Mapa + Overlay */}
          <section className="relative mt-16">
            <div className="relative w-full">
              <LeafletMap />
              <div className="absolute top-1/2 left-8 -translate-y-1/2 bg-white rounded-[40px] p-8 shadow-lg max-w-sm z-[10000]">
                <div className="grid gap-6">
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-[var(--brand)] rounded-full flex items-center justify-center flex-shrink-0">
                      <svg
                        className="w-6 h-6 text-black"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                      >
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                        <circle cx="12" cy="10" r="3" />
                      </svg>
                    </div>
                    <div>
                      <h4 className="unbounded-bold text-lg mb-2">ADRESSE</h4>
                      <p className="poppins text-neutral-800">
                        Flughafen 76/3
                        <br />
                        88046 Friedrichshafen
                        <br />
                        Deutschland
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-[var(--brand)] rounded-full flex items-center justify-center flex-shrink-0">
                      <svg
                        className="w-6 h-6 text-black"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                      >
                        <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7" />
                        <rect x="2" y="4" width="20" height="16" rx="2" />
                      </svg>
                    </div>
                    <div>
                      <h4 className="unbounded-bold text-lg mb-2">E-MAIL</h4>
                      <a
                        href="mailto:hallo@werbeinsel.de"
                        className="poppins underline"
                      >
                        hallo@werbeinsel.de
                      </a>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-[var(--brand)] rounded-full flex items-center justify-center flex-shrink-0">
                      <svg
                        className="w-6 h-6 text-black"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                      >
                        <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384" />
                      </svg>
                    </div>
                    <div>
                      <h4 className="unbounded-bold text-lg mb-2">TELEFON</h4>
                      <a
                        href="tel:+4975417005744"
                        className="poppins underline"
                      >
                        +49 7541 700 57 44
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </main>

      {/* Success overlay */}
      {showSuccess && (
        <div className="fixed inset-0 bg-[var(--brand)] z-50 flex items-center justify-center">
          <div className="text-center space-y-8">
            <div className="animate-bounce">
              <ThumbsUp className="w-48 h-48 mx-auto text-black" />
            </div>
            <div className="space-y-4">
              <h2 className="text-6xl unbounded-bold text-black">DANKE!</h2>
              <p className="text-2xl poppins text-black max-w-2xl mx-auto">
                Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze
                bei Ihnen.
              </p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
