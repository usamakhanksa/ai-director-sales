import { NextRequest, NextResponse } from 'next/server';
import { requireAuth } from '@/lib/auth';

/**
 * Dork Generation API Endpoint
 * 
 * POST /api/dorks/generate - Generate advanced search dorks
 * GET /api/dorks/templates - Get available dork templates
 */

export async function POST(request: NextRequest) {
  try {
    // Authenticate user using custom auth system
    const user = await requireAuth(request);
    const userId = user.id;
    

    // Get request body
    const body = await request.json();
    const { keyword, location, country, intent, platforms, language } = body;

    // Call backend service
    const backendResponse = await fetch(`${process.env.SCRAPER_BACKEND_URL}/v1/dorks/generate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${process.env.INTERNAL_API_SECRET}`,
        'X-User-ID': userId.toString(),
      },
      body: JSON.stringify({ keyword, location, country, intent, platforms, language }),
    });

    const result = await backendResponse.json();
    
    return new Response(JSON.stringify(result), {
      status: backendResponse.status,
      headers: { 'Content-Type': 'application/json' },
    });
  } catch (error: any) {
    if (error.status === 401) {
      return new Response(JSON.stringify({ success: false, error: 'Unauthorized' }), {
        status: 401,
        headers: { 'Content-Type': 'application/json' },
      });
    }
    
    console.error('Dork generation API error:', error);
    return new Response(JSON.stringify({ success: false, error: 'Internal server error' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' },
    });
  }
}

export async function GET(request: NextRequest) {
  try {
    // Authenticate user using custom auth system
    const user = await requireAuth(request);
    const userId = user.id;

    // Parse query parameters
    const { searchParams } = new URL(request.url);
    const action = searchParams.get('action') || 'templates';

    let backendUrl;
    if (action === 'history') {
      const limit = searchParams.get('limit') || '50';
      const offset = searchParams.get('offset') || '0';
      backendUrl = `${process.env.SCRAPER_BACKEND_URL}/v1/dorks/history?limit=${limit}&offset=${offset}`;
    } else {
      backendUrl = `${process.env.SCRAPER_BACKEND_URL}/v1/dorks/templates`;
    }

    // Call backend service
    const backendResponse = await fetch(backendUrl, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${process.env.INTERNAL_API_SECRET}`,
        'X-User-ID': userId.toString(),
      },
    });

    const result = await backendResponse.json();
    
    return new Response(JSON.stringify(result), {
      status: backendResponse.status,
      headers: { 'Content-Type': 'application/json' },
    });
  } catch (error: any) {
    if (error.status === 401) {
      return new Response(JSON.stringify({ success: false, error: 'Unauthorized' }), {
        status: 401,
        headers: { 'Content-Type': 'application/json' },
      });
    }
    
    console.error('Dork API GET error:', error);
    return new Response(JSON.stringify({ success: false, error: 'Internal server error' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' },
    });
  }
}