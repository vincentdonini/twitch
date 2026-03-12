import { ArrowLeft } from "lucide-react"
import { getTranslations } from "next-intl/server"
import { Button } from "@workspace/ui/components/button"

export default async function NotFound() {
  const t = await getTranslations("errors")

  return (
    <section className="flex items-center justify-center bg-muted/50 min-h-[calc(100vh-60px-80px)] md:min-h-[calc(100vh-60px-100px)] py-12">

      <div className='flex flex-col p-16'>
        <div className='mt-8 flex flex-1 flex-col items-center justify-center text-center xl:items-start xl:text-start'>
          <div className='mb-3 flex items-center gap-3'>
            <span className='text-sm font-semibold'>404</span>
          </div>
          <h1 className='mb-2 text-4xl font-bold'>{t("not_found_title")}</h1>
          <p>{t("not_found_description")}</p>
          <Button className='mt-8 flex cursor-pointer items-center gap-2'>
            <ArrowLeft className='size-4'></ArrowLeft>
            <span>{t("back_home")}</span>
          </Button>
        </div>
      </div>

    </section>
  )
}
