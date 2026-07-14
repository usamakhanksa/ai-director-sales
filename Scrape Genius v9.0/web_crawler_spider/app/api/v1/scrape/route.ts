import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';

export async function POST(req: NextRequest) {
  try {
    // Get API key from header or query param
    let apiKey = req.headers.get('x-api-key');
    if (!apiKey) {
      const { searchParams } = new URL(req.url);
      apiKey = searchParams.get('api_key');
    }

    if (!apiKey) {
      return NextResponse.json({ error: 'API key required' }, { status: 401 });
    }

    // Validate API key
    const keyRecord = await prisma.apiClientKey.findUnique({
      where: { key: apiKey },
      include: {
        user: true
      }
    });

    if (!keyRecord || !keyRecord.isActive || (keyRecord.expiresAt && new Date() > keyRecord.expiresAt)) {
      return NextResponse.json({ error: 'Invalid or expired API key' }, { status: 401 });
    }

    // Check rate limit
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const usageCount = await prisma.apiUsageLog.count({
      where: {
        apiKeyId: keyRecord.id,
        createdAt: {
          gte: today
        }
      }
    });

    if (usageCount >= keyRecord.rateLimit) {
      return NextResponse.json({ error: 'Rate limit exceeded' }, { status: 429 });
    }

    const body = await req.json();
    const { module, keywords, config = {} } = body;

    if (!module || !keywords) {
      return NextResponse.json({ error: 'Module and keywords are required' }, { status: 400 });
    }

    // Log API usage
    await prisma.apiUsageLog.create({
      data: {
        apiKeyId: keyRecord.id
      }
    });

    // Forward the request to the appropriate backend endpoint based on module
    let backendEndpoint = '';
    switch (module) {
      case 'instagram':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/instagram`;
        break;
      case 'google_maps':
        backendEndpoint = `${process.env.BACKEND_URL}/api/maps/search`;
        break;
      case 'facebook':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/facebook`;
        break;
      case 'linkedin':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/linkedin`;
        break;
      case 'twitter':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/twitter`;
        break;
      default:
        return NextResponse.json({ error: `Unsupported module: ${module}` }, { status: 400 });
    }

    // Forward request to backend with internal user ID
    const backendResponse = await fetch(backendEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Internal-User-Id': keyRecord.userId.toString(), // Internal header for user identification
      },
      body: JSON.stringify({ keywords, config })
    });

    const backendData = await backendResponse.json();
    
    if (!backendResponse.ok) {
      return NextResponse.json(backendData, { status: backendResponse.status });
    }

    return NextResponse.json(backendData);
  } catch (error) {
    console.error('Public API scrape error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}

// Get job status/results endpoint
export async function GET(req: NextRequest) {
  try {
    // Get API key from header or query param
    let apiKey = req.headers.get('x-api-key');
    if (!apiKey) {
      const { searchParams } = new URL(req.url);
      apiKey = searchParams.get('api_key');
    }

    if (!apiKey) {
      return NextResponse.json({ error: 'API key required' }, { status: 401 });
    }

    // Validate API key
    const keyRecord = await prisma.apiClientKey.findUnique({
      where: { key: apiKey }
    });

    if (!keyRecord || !keyRecord.isActive || (keyRecord.expiresAt && new Date() > keyRecord.expiresAt)) {
      return NextResponse.json({ error: 'Invalid or expired API key' }, { status: 401 });
    }

    const { searchParams } = new URL(req.url);
    const jobId = searchParams.get('jobId');
    const module = searchParams.get('module');

    if (!jobId || !module) {
      return NextResponse.json({ error: 'Job ID and module are required' }, { status: 400 });
    }

    // Log API usage
    await prisma.apiUsageLog.create({
      data: {
        apiKeyId: keyRecord.id
      }
    });

    // Forward request to backend
    let backendEndpoint = '';
    switch (module) {
      case 'instagram':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/instagram/${jobId}`;
        break;
      case 'google_maps':
        backendEndpoint = `${process.env.BACKEND_URL}/api/maps/job/${jobId}`;
        break;
      case 'facebook':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/facebook/${jobId}`;
        break;
      case 'linkedin':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/linkedin/${jobId}`;
        break;
      case 'twitter':
        backendEndpoint = `${process.env.BACKEND_URL}/api/social/twitter/${jobId}`;
        break;
      default:
        return NextResponse.json({ error: `Unsupported module: ${module}` }, { status: 400 });
    }

    const backendResponse = await fetch(backendEndpoint, {
      headers: {
        'X-Internal-User-Id': keyRecord.userId.toString(), // Internal header for user identification
      }
    });

    const backendData = await backendResponse.json();
    
    if (!backendResponse.ok) {
      return NextResponse.json(backendData, { status: backendResponse.status });
    }

    return NextResponse.json(backendData);
  } catch (error) {
    console.error('Public API get job error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}