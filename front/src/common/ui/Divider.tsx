import React from "react";

interface DividerProps {
    height?: string;
    color?: string;
}

const Divider: React.FC<DividerProps> = ({ height = "100%", color = "#282828" }) => {
    return (
        <div style={{ width: "1px", height, borderLeft: `1px solid ${color}` }} />
    );
};

export default Divider;
