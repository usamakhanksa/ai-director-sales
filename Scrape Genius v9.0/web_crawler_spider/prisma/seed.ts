import { PrismaClient, Role, ScrapeSource } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

function todayUtcDateOnly(): Date {
  const now = new Date();
  return new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate()));
}

async function upsertUser(params: {
  name: string;
  email: string;
  password: string;
  role: Role;
  isVerified: boolean;
}) {
  const passwordHash = await bcrypt.hash(params.password, 10);
  return prisma.user.upsert({
    where: { email: params.email },
    update: {},
    create: {
      name: params.name,
      email: params.email,
      passwordHash,
      role: params.role,
      isVerified: params.isVerified,
    },
  });
}

async function main() {
  console.log("Seeding admin + regular users...");
  const admin = await upsertUser({
    name: "ScrapeGenius Admin",
    email: "admin@scrapegenius.com",
    password: "AdminPass123!",
    role: Role.ADMIN,
    isVerified: true,
  });

  const user1 = await upsertUser({
    name: "Alice Johnson",
    email: "alice@example.com",
    password: "Password123!",
    role: Role.USER,
    isVerified: true,
  });

  const user2 = await upsertUser({
    name: "Bob Smith",
    email: "bob@example.com",
    password: "Password123!",
    role: Role.USER,
    isVerified: true,
  });

  console.log("Seeding API keys with varying usage...");
  const today = todayUtcDateOnly();

  // Shared pool of Google Custom Search keys, one nearly exhausted, one lightly used, one fresh.
  const key1 = await prisma.apiKey.create({
    data: {
      key: "REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_1",
      cx: "REPLACE_WITH_REAL_SEARCH_ENGINE_ID_1",
      dailyLimit: 100,
      isActive: true,
    },
  });
  await prisma.usageLog.create({
    data: { apiKeyId: key1.id, date: today, count: 92 },
  });

  const key2 = await prisma.apiKey.create({
    data: {
      key: "REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_2",
      cx: "REPLACE_WITH_REAL_SEARCH_ENGINE_ID_2",
      dailyLimit: 100,
      isActive: true,
    },
  });
  await prisma.usageLog.create({
    data: { apiKeyId: key2.id, date: today, count: 10 },
  });

  const key3 = await prisma.apiKey.create({
    data: {
      key: "REPLACE_WITH_REAL_GOOGLE_CUSTOM_SEARCH_KEY_3",
      cx: "REPLACE_WITH_REAL_SEARCH_ENGINE_ID_3",
      dailyLimit: 100,
      isActive: true,
    },
  });
  await prisma.usageLog.create({
    data: { apiKeyId: key3.id, date: today, count: 0 },
  });

  console.log("Seeding purchase codes...");
  const oneYearFromNow = new Date();
  oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1);

  // The code requested to be "properly functional": unassigned and ready to be
  // redeemed by any user via POST /api/purchase-code/activate.
  await prisma.purchaseCode.upsert({
    where: { code: "12345" },
    update: {},
    create: {
      code: "12345",
      isActive: false,
      userId: null,
      expiresAt: oneYearFromNow,
    },
  });

  // An already-activated example code, so the "activated" path has sample data too.
  await prisma.purchaseCode.upsert({
    where: { code: "ALICE-ACTIVATED-0001" },
    update: {},
    create: {
      code: "ALICE-ACTIVATED-0001",
      isActive: true,
      userId: user1.id,
      activatedAt: new Date(),
      expiresAt: oneYearFromNow,
    },
  });

  console.log("Seeding dashboard stats...");
  const aliceStats: Array<[string, number]> = [
    ["Emails Scraped", 482],
    ["Phone Numbers Scraped", 213],
    ["URLs Scraped", 1096],
    ["Search Records", 37],
  ];
  for (const [statType, recordCount] of aliceStats) {
    await prisma.dashboardStat.upsert({
      where: { userId_statType: { userId: user1.id, statType } },
      update: { recordCount },
      create: { userId: user1.id, statType, recordCount },
    });
  }

  const bobStats: Array<[string, number]> = [
    ["Emails Scraped", 54],
    ["Phone Numbers Scraped", 12],
    ["URLs Scraped", 201],
    ["Search Records", 6],
  ];
  for (const [statType, recordCount] of bobStats) {
    await prisma.dashboardStat.upsert({
      where: { userId_statType: { userId: user2.id, statType } },
      update: { recordCount },
      create: { userId: user2.id, statType, recordCount },
    });
  }

  console.log("Seeding a sample scraped record for Alice...");
  await prisma.scrapedRecord.create({
    data: {
      userId: user1.id,
      query: "digital marketing agencies in london",
      source: ScrapeSource.GOOGLE,
      data: [
        { link: "https://example-agency.com", title: "Example Agency" },
        { link: "https://another-agency.co.uk", title: "Another Agency" },
      ],
    },
  });

  console.log("✅ Seed complete.");
  console.log(`   Admin login: admin@scrapegenius.com / AdminPass123!`);
  console.log(`   User login:  alice@example.com / Password123!`);
  console.log(`   User login:  bob@example.com / Password123!`);
  console.log(`   Purchase code ready to activate: 12345`);
}

main()
  .catch((err) => {
    console.error(err);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
