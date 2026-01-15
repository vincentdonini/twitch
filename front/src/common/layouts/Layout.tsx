import React, {ReactNode} from 'react';

interface LayoutProps {
    leftComponent: ReactNode;
    centerComponent: ReactNode;
    rightComponent: ReactNode;
    drawerWidthLeft?: number;
    drawerWidthRight?: number;
}

const Layout: React.FC<LayoutProps> = (
    {
        leftComponent,
        centerComponent,
        rightComponent,
        drawerWidthLeft = 400,
        drawerWidthRight = 350,
    }
) => {

    const isLeftSideOpen = false;
    const isRightSideOpen = false;

    const height = 994;
    let width = 1766;
    const paddingX = 77;

    switch (true) {
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-expect-error
        case isLeftSideOpen && isRightSideOpen:
            break;
        case !isLeftSideOpen && !isRightSideOpen:
            width += drawerWidthLeft + drawerWidthRight;
            break;
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-expect-error
        case isLeftSideOpen && !isRightSideOpen:
            width += drawerWidthLeft - paddingX;
            break;
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-expect-error
        case !isLeftSideOpen && isRightSideOpen:
            width += drawerWidthLeft;
            break;
    }

    return (
        <>
            <div
                className="flex flex-col justify-space-between overflow-y-hidden relative -z-[1] transition-all duration-300"
                style={{
                    width: width,
                    height: height,
                    marginLeft: isLeftSideOpen ? 0 : `-${drawerWidthLeft}px`,
                }}
            >
                <div className="flex grow flex-row">
                    <div
                        className="sticky overflow-hidden flex-shrink-0 text-white bg-[rgba(16,16,16,0.8)] overflow-y-hidden"
                        style={{
                            width: drawerWidthLeft,
                        }}
                    >
                        {leftComponent}
                    </div>

                    <div className="grow"
                         style={{border:"10px solid green"}}
                    >
                        {centerComponent}
                    </div>

                    <div
                        className="sticky overflow-hidden flex-shrink-0 text-white bg-[rgba(16,16,16,0.8)] overflow-y-hidden"
                        style={{
                            width: drawerWidthRight,
                        }}
                    >
                        {rightComponent}
                    </div>
                </div>
            </div>
        </>
    );
};

export default Layout;
