"use client"

import ReactMarkdown from "react-markdown"

export function MarkdownSection({ title, content }: { title: string; content: string }) {
  return (
    <div className="space-y-3">
      <h2 className="text-xl font-semibold">{title}</h2>
      <div className="prose prose-sm dark:prose-invert max-w-none">
        <ReactMarkdown>{content}</ReactMarkdown>
      </div>
    </div>
  )
}
