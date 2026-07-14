import { NextRequest, NextResponse } from 'next/server';
import { verifyToken } from '@/lib/jwt';
import { prisma } from '@/lib/prisma';
import { v4 as uuidv4 } from 'uuid';

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
    const { name, rateLimit = 1000, expiresAt } = body;

    if (!name) {
      return NextResponse.json({ error: 'Name is required' }, { status: 400 });
    }

    // Generate a unique API key
    const apiKeyValue = `sg_${uuidv4().replace(/-/g, '')}`;

    // Create the API key in the database
    const apiKey = await prisma.apiClientKey.create({
      data: {
        userId: user.id,
        key: apiKeyValue,
        name,
        rateLimit,
        expiresAt: expiresAt ? new Date(expiresAt) : null,
        isActive: true
      }
    });

    // Return the API key (only the first time, not on subsequent requests)
    return NextResponse.json({ 
      success: true, 
      key: apiKey.key,
      name: apiKey.name,
      rateLimit: apiKey.rateLimit,
      createdAt: apiKey.createdAt
    });
  } catch (error) {
    console.error('API key creation error:', error);
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

    // Fetch all active API keys for the user
    const apiKeys = await prisma.apiClientKey.findMany({
      where: {
        userId: user.id,
        isActive: true
      },
      select: {
        id: true,
        name: true,
        rateLimit: true,
        createdAt: true,
        expiresAt: true
      },
      orderBy: {
        createdAt: 'desc'
      }
    });

    return NextResponse.json({ success: true, keys: apiKeys });
  } catch (error) {
    console.error('API key fetch error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
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
    const keyId = searchParams.get('id');

    if (!keyId) {
      return NextResponse.json({ error: 'Key ID is required' }, { status: 400 });
    }

    // Deactivate the API key (soft delete), scoped to the requesting user
    const existingKey = await prisma.apiClientKey.findUnique({ where: { id: parseInt(keyId) } });
    if (!existingKey || existingKey.userId !== user.id) {
      return NextResponse.json({ error: 'Key not found' }, { status: 404 });
    }

    await prisma.apiClientKey.update({
      where: { id: existingKey.id },
      data: {
        isActive: false
      }
    });

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error('API key deletion error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}