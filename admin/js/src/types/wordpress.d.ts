interface OrgManagerData {
    apiUrl: string;
    nonce: string;
}

declare global {
    interface Window {
        orgManagerData: OrgManagerData;
    }
}

export {}; 