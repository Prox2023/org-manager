import '@testing-library/jest-dom';
import { jest } from '@jest/globals';

declare global {
  const jest: typeof import('@jest/globals').jest;
  namespace jest {
    interface Mock<T = any, Y extends any[] = any> {
      (...args: Y): T;
      mockImplementation(fn: (...args: Y) => T): this;
      mockReturnValue(value: T): this;
      mockResolvedValue(value: Awaited<T>): this;
    }
  }
}

export {}; 