import React from "react";
import PlanningDay from "@modules/planning/ui/PlanningDay";
import {dayLabels, IConfig, IData} from "@modules/planning/domain/interface/IPlanning";

type Props = {
    config: IConfig;
    data: IData;
};

const Planning: React.FC<Props> = (
    {
        config,
        data,
    }
) => {
    if (!data) return null;

    const days = Object.keys(data);

    const fontTitle = config.fonts.title;

    return (
        <div id="calendar-week"
             className="flex flex-1 overflow-hidden"
             style={{
                 rowGap: config.gap,
                 columnGap: config.gap,
             }}
        >
            {days.map((day) => {
                const blocks = data[day as keyof IData];
                const hasData = blocks && blocks.length > 0;

                return (
                    <div
                        key={day}
                        className={`item-day flex flex-col flex-1 gap-3 ${!hasData ? "grow-0" : ""}`}
                    >
                        <div className="flex uppercase"
                             style={{
                                 fontFamily: `${fontTitle}, sans-serif`,
                                 color: config.colors.text,
                             }}
                        >
                            {dayLabels[day as keyof IData]}
                        </div>
                        <PlanningDay config={config} day={day} blocks={blocks ?? []}/>
                    </div>
                );
            })}
        </div>
    );
};


export default Planning;