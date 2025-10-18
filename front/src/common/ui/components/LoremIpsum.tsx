"use client";

import * as React from "react";
import Typography from "@mui/material/Typography";

type LoremIpsumProps = {
    length?: number;
};

const defaultText = `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed blandit nunc id lacinia gravida. Sed tincidunt pretium pharetra. Proin pellentesque nunc sed augue vulputate, in tempor eros fringilla. Morbi ac porta arcu, a dignissim velit. Integer sit amet aliquet massa, in ultrices dolor. Phasellus cursus lectus a molestie venenatis. Donec ut tincidunt nisi. Nullam egestas molestie malesuada. Suspendisse facilisis venenatis nibh eu vulputate. Maecenas a felis quis dui dapibus euismod. Curabitur a ex nibh. Sed fermentum nisi urna, in cursus nibh vestibulum vitae. Proin massa risus, fermentum id lobortis a, mollis sed nisl. Nullam imperdiet massa ipsum. Phasellus faucibus, nulla eu pellentesque tempor, tellus nulla consequat enim, sed tincidunt libero nunc eget sem. Cras elit leo, varius quis dignissim sed, mollis ut sapien. Ut vehicula elit eget tristique eleifend. Morbi vitae enim dolor. Morbi porttitor, ante congue scelerisque convallis, lectus metus laoreet augue, ac finibus quam massa sed diam. Proin quam enim, varius eu libero vestibulum, porttitor malesuada nisi. Suspendisse ipsum risus, luctus in diam eu, bibendum sodales magna. Vestibulum tempor magna et nibh vehicula convallis. Donec facilisis lorem dapibus elementum scelerisque. Cras ut sapien erat. Cras eget leo sem. Vivamus pharetra luctus ultricies. Sed feugiat tortor quis scelerisque imperdiet. Proin non dictum mauris, id aliquam lorem. Quisque sodales ac lorem et condimentum.`;

export default function LoremIpsum({length = 800}: LoremIpsumProps) {
    const text = defaultText.trim();

    const repeatedText = text.repeat(Math.ceil(length / text.length));
    const slicedText = repeatedText.slice(0, length);

    return (
        <Typography variant="body1" component="p">
            {slicedText}
        </Typography>
    );
}
