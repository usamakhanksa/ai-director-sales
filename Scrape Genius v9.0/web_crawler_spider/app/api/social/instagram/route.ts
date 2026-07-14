import { NextRequest, NextResponse } from 'next/server';
import { verifyToken } from '@/lib/jwt';
import { prisma } from '@/lib/prisma';

export async function POST(req: NextRequest) {
  try {
    const authHeader = req.headers.get('authorization');
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const token = authHeader.substring(7);
    let user;
    try {
      const payload = verifyToken(token);
      user = await prisma.user.findUnique({
        where: { id: payload.sub },
        select: { id: true, email: true, role: true }
      });
      
      if (!user) {
        return NextResponse.json({ error: 'User not found' }, { status: 401 });
      }
    } catch (error) {
      return NextResponse.json({ error: 'Invalid token' }, { status: 401 });
    }

    const body = await req.json();
    const { keywords, config = {} } = body;

    if (!keywords || !Array.isArray(keywords) || keywords.length === 0) {
      return NextResponse.json({ error: 'Keywords array is required' }, { status: 400 });
    }

    // Forward the request to the backend
    const backendResponse = await fetch(`${process.env.BACKEND_URL}/api/social/instagram`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': authHeader,
      },
      body: JSON.stringify({ keywords, config })
    });

    const backendData = await backendResponse.json();
    
    if (!backendResponse.ok) {
      return NextResponse.json(backendData, { status: backendResponse.status });
    }

    return NextResponse.json(backendData);
  } catch (error) {
    console.error('Instagram API error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}

export async function GET(req: NextRequest) {
  try {
    const authHeader = req.headers.get('authorization');
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const token = authHeader.substring(7);
    let user;
    try {
      const payload = verifyToken(token);
      user = await prisma.user.findUnique({
        where: { id: payload.sub },
        select: { id: true, email: true, role: true }
      });
      
      if (!user) {
        return NextResponse.json({ error: 'User not found' }, { status: 401 });
      }
    } catch (error) {
      return NextResponse.json({ error: 'Invalid token' }, { status: 401 });
    }

    const { searchParams } = new URL(req.url);
    const jobId = searchParams.get('jobId');

    if (!jobId) {
      return NextResponse.json({ error: 'Job ID is required' }, { status: 400 });
    }

    // Forward the request to the backend
    const backendResponse = await fetch(`${process.env.BACKEND_URL}/api/social/instagram/${jobId}`, {
      headers: {
        'Authorization': authHeader,
      }
    });

    const backendData = await backendResponse.json();
    
    if (!backendResponse.ok) {
      return NextResponse.json(backendData, { status: backendResponse.status });
    }

    return NextResponse.json(backendData);
  } catch (error) {
    console.error('Instagram GET API error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}