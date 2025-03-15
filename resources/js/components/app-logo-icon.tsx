import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Militair Ruimteschip Icoon">
            <circle cx="32" cy="32" r="30" stroke="#000" stroke-width="4" fill="none" />
            <path
                d="M32 6
         L37 22
         L54 22
         L40 32
         L46 48
         L32 38
         L18 48
         L24 32
         L10 22
         L27 22
         Z"
                fill="#000"
                opacity="0.1"
            />
            <path
                d="M32 14
         L30 20
         L26 24
         L24 28
         L24 36
         L26 38
         L30 40
         L32 46
         L34 40
         L38 38
         L40 36
         L40 28
         L38 24
         L34 20
         Z"
                fill="#000"
            />
        </svg>
    );
}
