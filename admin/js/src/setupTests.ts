import '@testing-library/jest-dom';

declare global {
  var orgManagerData: {
    apiUrl: string;
    nonce: string;
    debug: boolean;
  };
  var fetch: jest.Mock;
}

(globalThis as any).orgManagerData = {
  apiUrl: 'http://example.com/wp-json/org-manager/v1',
  nonce: 'test-nonce',
  debug: true
};

(globalThis as any).fetch = jest.fn().mockImplementation(() => 
  Promise.resolve({
    ok: true,
    json: () => Promise.resolve({}),
    status: 200,
    statusText: 'OK',
    headers: new Headers(),
  } as Response)
);

export {}; 