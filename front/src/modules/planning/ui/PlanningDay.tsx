import React from "react";
import PlanningBlock from "@modules/planning/ui/PlanningBlock";
import {IConfig, IDataBlock} from "@modules/planning/domain/interface/IPlanning";

type Props = {
    config: IConfig;
    day: string;
    blocks: IDataBlock[];
};

const PlanningDay: React.FC<Props> = (
    {
        config,
        day,
        blocks,
    }
) => {

    const textColor = config.colors.block.text;
    const borderColor = config.colors.block.background;

    const isRounded = config.block.isRounded;
    const radius = isRounded ? config.block.radius : 0;

    const isBorder = config.block.isBorder;
    const borderWidth = isBorder ? config.block.borderWidth : 0;

    return (
        <div id={`calendar-day-${day}`}
             className="relative flex flex-1 flex-col grow"
             style={{
                 height: '537px',
                 color: config.colors.text,
                 gap: config.gap,
             }}
        >
            {blocks && blocks.length > 0 ? (
                blocks.map((block, index) => (
                    <PlanningBlock
                        key={index}
                        config={config}
                        data={block}
                    />
                ))
            ) : (
                <div
                    className="relative flex flex-col flex-1 justify-between"
                    style={{
                        borderTopLeftRadius: radius,
                        borderTopRightRadius: radius,
                    }}
                >
                    <div
                        className="flex flex-col flex-1 justify-center items-center w-auto p-4 bg-black/50"
                        style={{
                            borderWidth: isBorder ? borderWidth : 0,
                            borderColor: borderColor,
                            borderRadius: radius,
                        }}
                    >
                        <div className="rotate-270 text-2xl"
                             style={{
                                 color: textColor,
                             }}
                        >
                            OFF
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PlanningDay;