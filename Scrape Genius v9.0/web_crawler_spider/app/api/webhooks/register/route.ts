import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { requireAuth } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

// GET reads the Authorization header (via requireAuth) — must never be statically prerendered.
export const dynamic = "force-dynamic";

// Validation schema for webhook registration
const registerWebhookSchema = z.object({
  url: z.string().url({ message: "URL must be a valid URL" }),
  events: z.array(z.enum(["JOB_STARTED", "JOB_COMPLETED", "JOB_FAILED", "EXPORT_READY", "SCRAPE_DATA_AVAILABLE"]))
    .min(1, { message: "At least one event must be selected" }),
  isActive: z.boolean().optional().default(true),
});

export async function POST(req: NextRequest): Promise<NextResponse> {
  try {
    // Authenticate the user
    const user = await requireAuth(req);
    
    // Parse and validate the request body
    const body = await req.json();
    const validatedData = registerWebhookSchema.parse(body);
    
    // Check if user already has a webhook for this URL
    const existingWebhook = await prisma.webhook.findFirst({
      where: {
        userId: user.id,
        url: validatedData.url,
      },
    });
    
    if (existingWebhook) {
      return NextResponse.json(
        { 
          success: false, 
          error: "Webhook URL already registered" 
        },
        { status: 409 }
      );
    }
    
    // Create the new webhook
    const webhook = await prisma.webhook.create({
      data: {
        userId: user.id,
        url: validatedData.url,
        events: validatedData.events,
        isActive: validatedData.isActive,
      },
      select: {
        id: true,
        url: true,
        events: true,
        isActive: true,
        createdAt: true,
        updatedAt: true,
      }
    });
    
    return NextResponse.json({
      success: true,
      data: webhook,
      message: "Webhook registered successfully"
    });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json(
        { 
          success: false, 
          error: "Invalid input", 
          details: error.errors 
        },
        { status: 400 }
      );
    }
    
    console.error("Webhook registration error:", error);
    return NextResponse.json(
      { 
        success: false, 
        error: "Internal server error" 
      },
      { status: 500 }
    );
  }
}

// GET endpoint to list user's webhooks
export async function GET(req: NextRequest): Promise<NextResponse> {
  try {
    // Authenticate the user
    const user = await requireAuth(req);
    
    // Get user's webhooks
    const webhooks = await prisma.webhook.findMany({
      where: {
        userId: user.id,
      },
      select: {
        id: true,
        url: true,
        events: true,
        isActive: true,
        createdAt: true,
        updatedAt: true,
      },
      orderBy: {
        createdAt: 'desc',
      }
    });
    
    return NextResponse.json({
      success: true,
      data: webhooks,
    });
  } catch (error) {
    console.error("Webhook listing error:", error);
    return NextResponse.json(
      { 
        success: false, 
        error: "Internal server error" 
      },
      { status: 500 }
    );
  }
}