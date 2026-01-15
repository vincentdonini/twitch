import React, {ReactNode} from 'react';

interface BackgroundBoxProps {
    background: string;
    children?: ReactNode;
}

const BackgroundBox: React.FC<BackgroundBoxProps> = ({background, children}) => {
    return (
        <div
            className="w-[1920px] h-[1080px] px-[77px] py-[56px] z-0"
            style={{
                backgroundImage: `url('/${background}')`,
                backgroundRepeat: 'no-repeat',
            }}
        >
            {children}
        </div>
    );
};

export default BackgroundBox;
