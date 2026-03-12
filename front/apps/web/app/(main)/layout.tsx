import { Navbar } from "@/ui/components/navbar"
import { Footer } from "@/ui/components/footer"

export default function MainLayout({ children }: { children: React.ReactNode }) {
  return (
    <>
      <Navbar />
      <main>
        {children}
      </main>
      <Footer />
    </>
  )
}
