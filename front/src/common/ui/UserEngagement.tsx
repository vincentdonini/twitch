import React, {useState, useEffect} from 'react';

interface AnimatedTextProps {
    title: string;
    text: string | null;
}

const UserEngagement: React.FC<AnimatedTextProps> = ({title, text}) => {
    const [currentText, setCurrentText] = useState(text);
    const [isAnimating, setIsAnimating] = useState(false);

    useEffect(() => {
        const newText = text ?? "...";
        if (newText !== currentText && newText !== process.env.NEXT_PUBLIC_TWITCH_CHANNEL_NAME) {
            setIsAnimating(true);

            setTimeout(() => {
                setCurrentText(newText);
                setIsAnimating(false);
            }, 500);
        }
    }, [text, currentText]);

    return (
        <>
            <div
                style={{
                    width: 360,
                    display: 'inline-flex',
                    flexDirection: 'row',
                    justifyContent: 'left',
                    gap: '10px',
                }}
            >
                <div
                    className={'heading-pro-regular primary-color'}
                    style={{
                        paddingTop: '5px',
                        paddingLeft: '10px',
                        textTransform: 'uppercase',
                        fontSize: '22px',
                    }}
                >
                    {title}
                </div>
                <div>
                    <div
                        className={`heading-pro-regular animated-text ${isAnimating ? 'fade-out' : 'fade-in'}`}
                        style={{
                            paddingTop: '3px',
                            whiteSpace: 'nowrap',
                            overflow: 'hidden',
                            textOverflow: 'ellipsis',
                            width: '100%',
                            textWrap: 'nowrap',
                        }}
                    >
                        {currentText}
                    </div>
                </div>

            </div>
        </>
    );
};

export default UserEngagement;