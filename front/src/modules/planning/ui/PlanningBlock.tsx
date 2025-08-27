import React from "react";
import {IConfig, IDataBlock} from "@modules/planning/domain/interface/IPlanning";

type Props = {
    config: IConfig;
    data: IDataBlock;
    className?: string;
};

const PlanningBlock: React.FC<Props> = (
    {
        config,
        data,
        className = "",
    }
) => {

    const isRounded = config.block.isRounded;
    const radius = isRounded ? config.block.radius : 0;
    const isBorder = config.block.isBorder;
    const borderWidth = isBorder ? config.block.borderWidth : 0;

    const hasVisuals = data.background;

    const titleColor = data.colors?.title ?? config.colors.block.title;
    const textColor = data.colors?.text ?? config.colors.block.text;
    const backgroundColor = data.colors?.background ?? config.colors.block.background;
    console.log(data.colors?.background, config.colors.primary, backgroundColor);

    return (
        <div
            className={
                `relative flex flex-col justify-between overflow-hidden ${
                    hasVisuals ? "flex-1" : ""
                } ${className}`
            }
            style={{
                borderRadius: radius,
            }}
        >
            {hasVisuals && (
                <div
                    className="flex flex-col flex-1 justify-between items-center overflow-hidden"
                    style={{
                        borderWidth: isBorder ? borderWidth : 0,
                        borderColor: backgroundColor,
                        borderTopLeftRadius: radius,
                        borderTopRightRadius: radius,
                        backgroundImage: `url('${data.background}')`,
                        backgroundSize: "cover",
                        backgroundPosition: "center",
                    }}
                >
                    {data.logo && (
                        <div className="w-full max-h-full flex justify-center items-start bg-gradient-to-b from-[rgba(0,0,0,0.8)] to-[rgba(0,0,0,0)]">
                            <img src={data.logo}
                                 alt={data.title}
                                 className="max-h-full z-10 p-4"
                            />
                        </div>
                    )}
                </div>
            )}
            <div
                className="flex flex-col w-auto p-4"
                style={{
                    backgroundColor: backgroundColor,
                    borderBottomLeftRadius: hasVisuals ? radius : 0,
                    borderBottomRightRadius: hasVisuals ? radius : 0,
                }}
            >
                <div className="text-xs"
                     style={{
                         color: titleColor,
                     }}
                >
                    {data.title}
                </div>
                <div className="text-base"
                     style={{
                         color: textColor,
                     }}
                >
                    {data.hours}
                </div>
            </div>
        </div>
    );
};

export default PlanningBlock;
