import React from "react";

type Props = {
    partnerships: Record<string, string>;
};

const PartnershipBlocks: React.FC<Props> = ({partnerships}) => {
    return (
        <div className="flex flex-row gap-5 text-white h-[30px]">
            {Object.entries(partnerships).map(([brand, img]) => (
                    <img
                        key={brand}
                        className="h-auto max-h-[30px]"
                        alt={brand}
                        src={img}
                    />
                )
            )}
        </div>
    );
};

export default PartnershipBlocks;
