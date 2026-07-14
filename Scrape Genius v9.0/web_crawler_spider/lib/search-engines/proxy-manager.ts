import { ProxyAgent, type Dispatcher } from "undici";

/**
 * Round-robins through PROXY_LIST (comma-separated proxy URLs, e.g.
 * "http://user:pass@host:port,http://host2:port2") for scraper fallback
 * requests. Returns undefined (direct connection) when unconfigured.
 */
class ProxyManager {
  private proxies: string[];
  private cursor = 0;
  private agents = new Map<string, Dispatcher>();

  constructor() {
    this.proxies = (process.env.PROXY_LIST || "")
      .split(",")
      .map((p) => p.trim())
      .filter(Boolean);
  }

  hasProxies(): boolean {
    return this.proxies.length > 0;
  }

  next(): Dispatcher | undefined {
    if (this.proxies.length === 0) return undefined;
    const url = this.proxies[this.cursor % this.proxies.length];
    this.cursor += 1;

    let agent = this.agents.get(url);
    if (!agent) {
      agent = new ProxyAgent(url);
      this.agents.set(url, agent);
    }
    return agent;
  }
}

export const proxyManager = new ProxyManager();
